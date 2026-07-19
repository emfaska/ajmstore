<?php

namespace App\Services;

use App\Models\Purchase;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseService extends BaseService
{
    /**
     * @var PurchaseRepositoryInterface
     */
    protected PurchaseRepositoryInterface $purchaseRepository;

    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * PurchaseService constructor.
     *
     * @param PurchaseRepositoryInterface $purchaseRepository
     * @param ProductService $productService
     */
    public function __construct(
        PurchaseRepositoryInterface $purchaseRepository,
        ProductService $productService
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->productService = $productService;
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
            // 1. Simpan pembelian (Save main purchase)
            $purchase = $this->purchaseRepository->create($purchaseData);

            // Process each item
            $this->processPurchaseItems($purchase, $items);

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

            // 2. Detail pembelian (Save purchase item)
            $subtotal = $quantity * $costPrice;
            $purchase->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'cost_price' => $costPrice,
                'subtotal'   => $subtotal,
            ]);

            // 3. Update stok (Update product stock)
            $this->updateProductStock($productId, $quantity);

            // 4. Update harga modal (Update capital/purchase price)
            $this->productService->updatePurchasePrice($productId, $costPrice);
        }
    }

    /**
     * Update product stock by adding the purchased quantity.
     *
     * @param int|string $productId
     * @param int $addedQuantity
     * @return void
     */
    protected function updateProductStock(int|string $productId, int $addedQuantity): void
    {
        $product = $this->productService->getProductById($productId);
        
        $newStock = $product->stock + $addedQuantity;
        
        $this->productService->updateProduct($productId, [
            'stock' => $newStock,
        ]);
    }
}
