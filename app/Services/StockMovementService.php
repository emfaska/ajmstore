<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class StockMovementService extends BaseService
{
    /**
     * @var StockMovementRepositoryInterface
     */
    protected StockMovementRepositoryInterface $stockMovementRepository;

    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * StockMovementService constructor.
     *
     * @param StockMovementRepositoryInterface $stockMovementRepository
     * @param ProductService $productService
     */
    public function __construct(
        StockMovementRepositoryInterface $stockMovementRepository,
        ProductService $productService
    ) {
        $this->stockMovementRepository = $stockMovementRepository;
        $this->productService = $productService;
    }

    /**
     * Mencatat mutasi stok masuk (Stock In) dan memperbarui stok produk.
     *
     * @param int|string $productId
     * @param int $quantity
     * @param string|null $description
     * @param string|null $referenceType
     * @param int|string|null $referenceId
     * @return StockMovement
     * @throws Exception
     */
    public function recordIn(
        int|string $productId, 
        int $quantity, 
        ?string $description = null, 
        ?string $referenceType = null, 
        int|string|null $referenceId = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Kuantitas stok masuk harus lebih besar dari 0.");
        }

        try {
            return DB::transaction(function () use ($productId, $quantity, $description, $referenceType, $referenceId) {
                // Ambil data produk
                $product = $this->productService->getProductById($productId);
                
                // Tambahkan stok
                $this->productService->updateProduct($productId, [
                    'stock' => $product->stock + $quantity
                ]);

                // Catat history
                return $this->stockMovementRepository->create([
                    'product_id'         => $productId,
                    'type'               => 'in',
                    'quantity'           => $quantity,
                    'description'        => $description ?? 'Stok masuk',
                    'referenceable_type' => $referenceType,
                    'referenceable_id'   => $referenceId,
                ]);
            });
        } catch (Exception $e) {
            Log::error("Gagal mencatat stok masuk.", ['error' => $e->getMessage()]);
            throw new Exception("Terjadi kesalahan sistem saat mencatat stok masuk.");
        }
    }

    /**
     * Mencatat mutasi stok keluar (Stock Out) dan mengurangi stok produk.
     *
     * @param int|string $productId
     * @param int $quantity
     * @param string|null $description
     * @param string|null $referenceType
     * @param int|string|null $referenceId
     * @return StockMovement
     * @throws Exception
     */
    public function recordOut(
        int|string $productId, 
        int $quantity, 
        ?string $description = null, 
        ?string $referenceType = null, 
        int|string|null $referenceId = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Kuantitas stok keluar harus lebih besar dari 0.");
        }

        // Validasi agar stok tidak menjadi negatif
        if (!$this->productService->validateStock($productId, $quantity)) {
            throw new InvalidArgumentException("Stok produk tidak mencukupi untuk melakukan pengeluaran sebesar {$quantity}.");
        }

        try {
            return DB::transaction(function () use ($productId, $quantity, $description, $referenceType, $referenceId) {
                $product = $this->productService->getProductById($productId);
                
                $this->productService->updateProduct($productId, [
                    'stock' => $product->stock - $quantity
                ]);

                return $this->stockMovementRepository->create([
                    'product_id'         => $productId,
                    'type'               => 'out',
                    'quantity'           => $quantity,
                    'description'        => $description ?? 'Stok keluar',
                    'referenceable_type' => $referenceType,
                    'referenceable_id'   => $referenceId,
                ]);
            });
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Gagal mencatat stok keluar.", ['error' => $e->getMessage()]);
            throw new Exception("Terjadi kesalahan sistem saat mencatat stok keluar.");
        }
    }

    /**
     * Mencatat penyesuaian stok (Stock Adjustment).
     *
     * @param int|string $productId
     * @param int $difference Nilai selisih stok (bisa positif atau negatif).
     * @param string|null $description
     * @return StockMovement
     * @throws Exception
     */
    public function recordAdjustment(int|string $productId, int $difference, ?string $description = null): StockMovement
    {
        if ($difference === 0) {
            throw new InvalidArgumentException("Selisih penyesuaian stok tidak boleh 0.");
        }

        try {
            return DB::transaction(function () use ($productId, $difference, $description) {
                $product = $this->productService->getProductById($productId);
                
                $newStock = $product->stock + $difference;

                if ($newStock < 0) {
                    throw new InvalidArgumentException("Penyesuaian stok gagal. Stok akhir tidak boleh kurang dari 0.");
                }

                $this->productService->updateProduct($productId, [
                    'stock' => $newStock
                ]);

                return $this->stockMovementRepository->create([
                    'product_id'  => $productId,
                    'type'        => 'adjustment',
                    'quantity'    => $difference,
                    'description' => $description ?? 'Penyesuaian stok (Adjustment)',
                ]);
            });
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Gagal mencatat penyesuaian stok.", ['error' => $e->getMessage()]);
            throw new Exception("Terjadi kesalahan sistem saat melakukan penyesuaian stok.");
        }
    }

    /**
     * Mengambil riwayat mutasi stok berdasarkan ID produk.
     *
     * @param int|string $productId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getHistoryByProduct(int|string $productId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockMovementRepository->getByProduct((int) $productId, $perPage);
    }

    /**
     * Filter riwayat mutasi berdasarkan rentang tanggal.
     * Menggunakan model StockMovement secara langsung karena query date-range tidak ada di Repository.
     *
     * @param string $from Format Y-m-d
     * @param string $to Format Y-m-d
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filterByDate(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return StockMovement::with('product')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Menghitung total keseluruhan kuantitas stok yang masuk pada produk tertentu.
     *
     * @param int|string $productId
     * @return int
     */
    public function getTotalIn(int|string $productId): int
    {
        return (int) StockMovement::forProduct((int) $productId)
            ->in()
            ->sum('quantity');
    }

    /**
     * Menghitung total keseluruhan kuantitas stok yang keluar pada produk tertentu.
     *
     * @param int|string $productId
     * @return int
     */
    public function getTotalOut(int|string $productId): int
    {
        return (int) StockMovement::forProduct((int) $productId)
            ->out()
            ->sum('quantity');
    }
}
