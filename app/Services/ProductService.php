<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    protected $productRepository;
    protected $cacheService;
    protected $cachePrefix = 'product_';

    public function __construct(ProductRepository $productRepository, CacheService $cacheService)
    {
        $this->productRepository = $productRepository;
        $this->cacheService = $cacheService;
    }

    public function getAllProducts(array $options = [])
    {
        $cacheKey = $this->generateCacheKey('all', $options);
        return $this->cacheService->remember($cacheKey, function () use ($options) {
            $paginator = $this->productRepository->all($options);
            return [
                'data' => $paginator->items(),
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ];
        });
    }

    public function getProductById($id)
    {
        $cacheKey = "{$this->cachePrefix}find_{$id}";
        return $this->cacheService->remember($cacheKey, function () use ($id) {
            return $this->productRepository->find($id);
        });
    }

    public function createProduct(array $data)
    {
        $this->validateCategory($data['category_id'] ?? null);
        $product = $this->productRepository->create($data);
        $this->cacheService->forget($this->generateCacheKey('all'));
        $this->cacheService->forget($this->generateCacheKey('search'));

        return $product;
    }

    public function updateProduct(array $data, $id)
    {
        $this->validateCategory($data['category_id'] ?? null);
        $product = $this->productRepository->update($data, $id);
        
        $this->cacheService->forget("{$this->cachePrefix}find_{$id}");
        $this->cacheService->forget($this->generateCacheKey('all'));
        $this->cacheService->forget($this->generateCacheKey('search'));

        return $product;
    }

    public function deleteProduct($id)
    {
        $this->productRepository->delete($id);

        $this->cacheService->forget("{$this->cachePrefix}find_{$id}");
        $this->cacheService->forget($this->generateCacheKey('all'));
        $this->cacheService->forget($this->generateCacheKey('search'));

        return true; // Return a value for the controller response
    }

    public function searchProducts($search = null, $categoryId = null, array $options = [])
    {
        $this->validateCategory($categoryId);
        $cacheKey = $this->generateCacheKey('search', $options, compact('search', 'categoryId'));
        return $this->cacheService->remember($cacheKey, function () use ($search, $categoryId, $options) {
            $paginator = $this->productRepository->search($search, $categoryId, $options);
            return [
                'data' => $paginator->items(),
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ];
        });
    }

    protected function generateCacheKey($prefix, $options = [], $additionalData = [])
    {
        return "{$this->cachePrefix}{$prefix}_" . md5(json_encode(array_merge($options, $additionalData)));
    }

    protected function validateCategory($categoryId)
    {
        if ($categoryId && !\App\Models\Category::where('id', $categoryId)->exists()) {
            throw new \InvalidArgumentException('Invalid category ID', 409);
        }
    }
}