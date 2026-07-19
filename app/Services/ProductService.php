<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class ProductService extends BaseService
{
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * ProductService constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get all products.
     *
     * @param array $with
     * @return Collection
     */
    public function getAllProducts(array $with = []): Collection
    {
        return $this->productRepository->all($with);
    }

    /**
     * Get paginated products.
     *
     * @param int $perPage
     * @param array $with
     * @return LengthAwarePaginator
     */
    public function getProductsPaginated(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage, $with);
    }

    /**
     * Find product by ID.
     *
     * @param int|string $id
     * @param array $with
     * @return Model
     */
    public function getProductById(int|string $id, array $with = []): Model
    {
        return $this->productRepository->findOrFail($id, $with);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return Model
     */
    public function createProduct(array $data): Model
    {
        return $this->productRepository->create($data);
    }

    /**
     * Update an existing product.
     *
     * @param int|string $id
     * @param array $data
     * @return Model
     */
    public function updateProduct(int|string $id, array $data): Model
    {
        return $this->productRepository->update($id, $data);
    }

    /**
     * Delete a product.
     *
     * @param int|string $id
     * @return bool
     */
    public function deleteProduct(int|string $id): bool
    {
        return $this->productRepository->delete($id);
    }

    /**
     * Validate if the product has sufficient stock.
     *
     * @param int|string $id
     * @param int $quantityToDeduct
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateStock(int|string $id, int $quantityToDeduct): bool
    {
        if ($quantityToDeduct <= 0) {
            throw new InvalidArgumentException("Quantity to deduct must be greater than zero.");
        }

        $product = $this->productRepository->findOrFail($id);

        return $product->stock >= $quantityToDeduct;
    }

    /**
     * Check if product is at or below minimum stock.
     *
     * @param int|string $id
     * @return bool
     */
    public function checkMinimumStock(int|string $id): bool
    {
        $product = $this->productRepository->findOrFail($id);

        // Uses the accessor 'is_low_stock' defined in Product model,
        // or can evaluate manually: return $product->stock <= $product->min_stock;
        return $product->is_low_stock;
    }

    /**
     * Update product purchase price (harga modal).
     *
     * @param int|string $id
     * @param float $purchasePrice
     * @return Model
     * @throws InvalidArgumentException
     */
    public function updatePurchasePrice(int|string $id, float $purchasePrice): Model
    {
        if ($purchasePrice < 0) {
            throw new InvalidArgumentException("Purchase price cannot be negative.");
        }

        return $this->productRepository->update($id, [
            'purchase_price' => $purchasePrice
        ]);
    }

    /**
     * Update product sale price (harga jual).
     *
     * @param int|string $id
     * @param float $salePrice
     * @return Model
     * @throws InvalidArgumentException
     */
    public function updateSalePrice(int|string $id, float $salePrice): Model
    {
        if ($salePrice < 0) {
            throw new InvalidArgumentException("Sale price cannot be negative.");
        }

        return $this->productRepository->update($id, [
            'sale_price' => $salePrice
        ]);
    }
}
