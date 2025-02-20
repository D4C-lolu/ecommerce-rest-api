<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Services\CacheService;

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
            return $this->productRepository->all($options);
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
        $product = $this->productRepository->create($data);
        $this->cacheService->forget($this->generateCacheKey('all'));

        return $product;
    }

    public function updateProduct(array $data, $id)
    {
        $product = $this->productRepository->update($data, $id);

        $this->cacheService->forget("{$this->cachePrefix}find_{$id}");
        $this->cacheService->forget($this->generateCacheKey('all'));

        return $product;
    }

    public function deleteProduct($id)
    {
        $this->productRepository->delete($id);

        $this->cacheService->forget("{$this->cachePrefix}find_{$id}");
        $this->cacheService->forget($this->generateCacheKey('all'));
    }

    public function searchProducts($search = null, $categoryId = null, array $options = [])
    {
        $cacheKey = $this->generateCacheKey('search', $options, compact('search', 'categoryId'));
        return $this->cacheService->remember($cacheKey, function () use ($search, $categoryId, $options) {
            return $this->productRepository->search($search, $categoryId, $options);
        });
    }

    protected function generateCacheKey($prefix, $options, $additionalData = [])
    {
        return "{$this->cachePrefix}{$prefix}_" . md5(json_encode(array_merge($options, $additionalData)));
    }
}
