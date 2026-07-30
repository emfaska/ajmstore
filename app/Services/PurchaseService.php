<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PurchaseService extends BaseService
{
    /**
     * @var PurchaseRepositoryInterface
     */
    protected PurchaseRepositoryInterface $purchaseRepository;

    /**
     * @var StockMovementRepositoryInterface
     */
    protected StockMovementRepositoryInterface $stockMovementRepository;

    /**
     * @var CashTransactionRepositoryInterface
     */
    protected CashTransactionRepositoryInterface $cashTransactionRepository;

    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * PurchaseService constructor.
     */
    public function __construct(
        PurchaseRepositoryInterface $purchaseRepository,
        ProductService $productService,
        StockMovementRepositoryInterface $stockMovementRepository,
        CashTransactionRepositoryInterface $cashTransactionRepository
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->productService = $productService;
        $this->stockMovementRepository = $stockMovementRepository;
        $this->cashTransactionRepository = $cashTransactionRepository;
    }

    /**
     * Get paginated purchases.
     */
    public function getPurchasesPaginated(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->purchaseRepository->paginate($perPage, $with);
    }

    /**
     * Get advanced filtered purchases.
     */
    public function advancedSearchPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->purchaseRepository->advancedSearch($filters, $perPage);
    }

    /**
     * Get purchase by ID.
     */
    public function getPurchaseById(int|string $id, array $with = []): Model
    {
        return $this->purchaseRepository->findOrFail($id, $with);
    }

    /**
     * Create a new purchase with items, update product stock and capital price.
     * This entire process is wrapped in a database transaction.
     *
     * @param array $purchaseData Data for the main purchase record
     * @param array $items Array of purchase items data
     * @return Purchase
     * @throws \Exception
     */
    public function createPurchase(array $purchaseData, array $items): Purchase
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Purchase items cannot be empty.');
        }

        return DB::transaction(function () use ($purchaseData, $items) {
            // Hitung total amount
            $totalAmount = 0;
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['cost_price'] ?? 0);
                $totalAmount += $qty * $price;
            }

            $purchaseData['total_amount'] = $totalAmount;

            // 1. Simpan pembelian (Save main purchase)
            $purchase = $this->purchaseRepository->create($purchaseData);

            // Process each item
            $this->processPurchaseItems($purchase, $items);

            // Jika status pembayaran Lunas, buat Cash Transaction bertipe credit (kas keluar)
            if (($purchaseData['payment_status'] ?? '') === 'paid') {
                $this->cashTransactionRepository->create([
                    'payment_method_id'  => $purchaseData['payment_method_id'] ?? null,
                    'type'               => 'credit', // Credit represents outflow (Kas Keluar)
                    'amount'             => $totalAmount,
                    'description'        => "Pembayaran pembelian invoice: {$purchase->invoice_number}",
                    'referenceable_type' => Purchase::class,
                    'referenceable_id'   => $purchase->id,
                    'transaction_date'   => $purchaseData['purchase_date'] ?? now()->toDateString(),
                ]);
            }

            return $purchase;
        });
    }

    /**
     * Update an existing purchase transaction.
     * Reverts old stock changes if old status was completed, deletes old details,
     * and saves updated info.
     */
    public function updatePurchase(int|string $id, array $purchaseData, array $items): Purchase
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Purchase items cannot be empty.');
        }

        return DB::transaction(function () use ($id, $purchaseData, $items) {
            $purchase = $this->purchaseRepository->findOrFail($id, ['items']);

            // Revert stock if old purchase status was completed
            if ($purchase->status === 'completed') {
                foreach ($purchase->items as $oldItem) {
                    $this->updateProductStock($oldItem->product_id, -$oldItem->quantity);
                }
            }

            // Delete old details
            foreach ($purchase->items as $oldItem) {
                // Delete stock movements associated with old items
                $this->stockMovementRepository->model->newQuery()
                    ->where('referenceable_type', PurchaseItem::class)
                    ->where('referenceable_id', $oldItem->id)
                    ->delete();
                $oldItem->delete();
            }

            // Delete old cash transactions associated with this purchase
            $this->cashTransactionRepository->model->newQuery()
                ->where('referenceable_type', Purchase::class)
                ->where('referenceable_id', $purchase->id)
                ->delete();

            // Hitung total amount baru
            $totalAmount = 0;
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['cost_price'] ?? 0);
                $totalAmount += $qty * $price;
            }

            $purchaseData['total_amount'] = $totalAmount;

            // Update main purchase record
            $purchase = $this->purchaseRepository->update($purchase->id, $purchaseData);

            // Save new items
            $this->processPurchaseItems($purchase, $items);

            // Record cash transaction if payment status is paid (Lunas)
            if (($purchaseData['payment_status'] ?? '') === 'paid') {
                $this->cashTransactionRepository->create([
                    'payment_method_id'  => $purchaseData['payment_method_id'] ?? null,
                    'type'               => 'credit', // Credit represents outflow (Kas Keluar)
                    'amount'             => $totalAmount,
                    'description'        => "Pembayaran pembelian invoice: {$purchase->invoice_number}",
                    'referenceable_type' => Purchase::class,
                    'referenceable_id'   => $purchase->id,
                    'transaction_date'   => $purchaseData['purchase_date'] ?? now()->toDateString(),
                ]);
            }

            return $purchase;
        });
    }

    /**
     * Process details of the purchase, updating associated product states.
     *
     * @param Purchase $purchase
     * @param array $items
     * @return void
     */
    protected function processPurchaseItems(Purchase $purchase, array $items): void
    {
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $quantity = (int) ($item['quantity'] ?? 0);
            $costPrice = (float) ($item['cost_price'] ?? 0);

            if ($quantity <= 0) {
                throw new InvalidArgumentException("Purchase quantity must be greater than zero for product ID {$productId}.");
            }
            if ($costPrice < 0) {
                throw new InvalidArgumentException("Cost price cannot be negative for product ID {$productId}.");
            }

            $subtotal = $quantity * $costPrice;
            $purchaseItem = $purchase->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'cost_price' => $costPrice,
                'subtotal'   => $subtotal,
            ]);

            // Hanya update stock & record movement jika status pembelian 'completed'
            if ($purchase->status === 'completed') {
                // 3. Update stok (Update product stock)
                $this->updateProductStock($productId, $quantity);

                // 4. Update harga modal (Update capital/purchase price)
                $this->productService->updatePurchasePrice($productId, $costPrice);

                // Catat Stock Movement
                $this->stockMovementRepository->create([
                    'product_id'         => $productId,
                    'type'               => 'in',
                    'quantity'           => $quantity,
                    'referenceable_type' => PurchaseItem::class,
                    'referenceable_id'   => $purchaseItem->id,
                    'description'        => "Pembelian invoice: {$purchase->invoice_number}",
                ]);
            }
        }
    }

    /**
     * Update product stock.
     */
    protected function updateProductStock(int|string $productId, int $addedQuantity): void
    {
        $product = $this->productService->getProductById($productId);
        
        $newStock = $product->stock + $addedQuantity;
        
        $this->productService->updateProduct($productId, [
            'stock' => $newStock,
        ]);
    }

    /**
     * Soft delete purchase.
     */
    public function deletePurchase(int|string $id): bool
    {
        return $this->purchaseRepository->delete($id);
    }

    /**
     * Restore soft deleted purchase.
     */
    public function restorePurchase(int|string $id): bool
    {
        return $this->purchaseRepository->restore($id);
    }
}
