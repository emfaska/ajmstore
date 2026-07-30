<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService extends BaseService
{
    /**
     * @var SaleRepositoryInterface
     */
    protected SaleRepositoryInterface $saleRepository;

    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * @var StockMovementRepositoryInterface
     */
    protected StockMovementRepositoryInterface $stockMovementRepository;

    /**
     * @var CashTransactionRepositoryInterface
     */
    protected CashTransactionRepositoryInterface $cashTransactionRepository;

    /**
     * SaleService constructor.
     *
     * @param SaleRepositoryInterface $saleRepository
     * @param ProductService $productService
     * @param StockMovementRepositoryInterface $stockMovementRepository
     * @param CashTransactionRepositoryInterface $cashTransactionRepository
     */
    public function __construct(
        SaleRepositoryInterface $saleRepository,
        ProductService $productService,
        StockMovementRepositoryInterface $stockMovementRepository,
        CashTransactionRepositoryInterface $cashTransactionRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->productService = $productService;
        $this->stockMovementRepository = $stockMovementRepository;
        $this->cashTransactionRepository = $cashTransactionRepository;
    }

    /**
     * Create a new sale transaction including items, stock updates, stock movements,
     * and cash transactions, all wrapped in a database transaction.
     *
     * @param array $saleData
     * @param array $items
     * @return Sale
     * @throws \Exception
     */
    public function createSale(array $saleData, array $items): Sale
    {
        if (empty($items)) {
            throw new InvalidArgumentException("Sale items cannot be empty.");
        }

        return DB::transaction(function () use ($saleData, $items) {
            $subtotal = 0;
            $discount = (float) ($saleData['discount'] ?? 0);
            $tax = (float) ($saleData['tax'] ?? 0);

            // Validasi Stok & Hitung Subtotal untuk tiap item
            $processedItems = [];
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = (int) ($item['quantity'] ?? 0);
                $sellingPrice = (float) ($item['selling_price'] ?? 0);

                if ($quantity <= 0) {
                    throw new InvalidArgumentException("Quantity must be greater than zero for product ID {$productId}.");
                }

                // 1. Validasi stok (menggunakan fungsi dari ProductService)
                if (!$this->productService->validateStock($productId, $quantity)) {
                    throw new InvalidArgumentException("Insufficient stock for product ID {$productId}.");
                }

                // 2 & 3. Hitung subtotal item & akumulasi subtotal transaksi
                $itemSubtotal = $quantity * $sellingPrice;
                $subtotal += $itemSubtotal;

                $processedItems[] = [
                    'product_id'    => $productId,
                    'quantity'      => $quantity,
                    'selling_price' => $sellingPrice,
                    'subtotal'      => $itemSubtotal,
                ];
            }

            // 4 & 5. Hitung diskon & grand total
            $totalAmount = $subtotal - $discount + $tax;

            if ($totalAmount < 0) {
                throw new InvalidArgumentException("Grand total cannot be negative.");
            }

            // Memperbarui array data penjualan untuk disimpan
            $saleData['subtotal'] = $subtotal;
            $saleData['total_amount'] = $totalAmount;

            // 6. Transaksi Penjualan (Simpan data parent ke tabel sales)
            $sale = $this->saleRepository->create($saleData);

            // Proses tiap item untuk disimpan dan mengupdate stok
            foreach ($processedItems as $itemData) {
                // Simpan SaleItem
                $saleItem = $sale->items()->create($itemData);

                // 7. Kurangi Stok
                $this->deductProductStock($itemData['product_id'], $itemData['quantity']);

                // 8. Simpan Stock Movement (mencatat riwayat barang keluar)
                $this->stockMovementRepository->create([
                    'product_id'         => $itemData['product_id'],
                    'type'               => 'out',
                    'quantity'           => $itemData['quantity'],
                    'referenceable_type' => SaleItem::class,
                    'referenceable_id'   => $saleItem->id,
                    'description'        => "Penjualan invoice: {$sale->invoice_number}",
                ]);
            }

            // 9. Simpan Cash Transaction
            // Dicatat sebagai debit (pemasukan) jika status penjualan adalah paid
            if (($saleData['payment_status'] ?? '') === 'paid') {
                $this->cashTransactionRepository->create([
                    'payment_method_id'  => $saleData['payment_method_id'] ?? null,
                    'type'               => 'debit',
                    'amount'             => $totalAmount,
                    'description'        => "Pembayaran penjualan invoice: {$sale->invoice_number}",
                    'referenceable_type' => Sale::class,
                    'referenceable_id'   => $sale->id,
                    'transaction_date'   => $saleData['sale_date'] ?? now()->toDateString(),
                ]);
            }

            return $sale;
        });
    }

    /**
     * Deduct product stock by the sold quantity.
     *
     * @param int|string $productId
     * @param int $deductedQuantity
     * @return void
     */
    protected function deductProductStock(int|string $productId, int $deductedQuantity): void
    {
        $product = $this->productService->getProductById($productId);
        
        $newStock = $product->stock - $deductedQuantity;
        
        $this->productService->updateProduct($productId, [
            'stock' => $newStock,
        ]);
    }

    /**
     * Get sales report aggregates and matching listings.
     */
    public function getSalesReport(array $filters): array
    {
        $sales = $this->saleRepository->getSalesReportData($filters);

        $totalOmzet = 0.0;
        $totalItemTerjual = 0;
        $totalLaba = 0.0;

        foreach ($sales as $sale) {
            $totalOmzet += (float) $sale->total_amount;

            $saleLaba = 0.0;
            foreach ($sale->items as $item) {
                $totalItemTerjual += $item->quantity;
                $purchasePrice = (float) ($item->product->purchase_price ?? 0);
                $sellingPrice = (float) $item->selling_price;
                
                // Laba item = (harga jual - harga beli) * qty
                $itemLaba = ($sellingPrice - $purchasePrice) * $item->quantity;
                $saleLaba += $itemLaba;
            }

            // Kurangi diskon invoice level
            $saleLaba -= (float) $sale->discount;
            $totalLaba += $saleLaba;
        }

        return [
            'sales' => $sales,
            'total_omzet' => $totalOmzet,
            'total_item_terjual' => $totalItemTerjual,
            'total_laba' => $totalLaba,
        ];
    }
}
