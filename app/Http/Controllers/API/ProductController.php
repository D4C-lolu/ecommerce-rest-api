<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\{
    ProductStoreRequest,
    ProductUpdateRequest,
    ProductSearchRequest
};
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductSearchRequest $request)
    {
        try {
            return response()->json(
                $this->productService->getAllProducts($request->validated())
            );
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            return response()->json($product, 201);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        try {
            $product = $this->productService->updateProduct($request->validated(), $id);
            return response()->json($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(ProductSearchRequest $request)
    {
        try {
            $validated = $request->validated();
            return response()->json($this->productService->searchProducts(
                $validated['search'] ?? null,
                $validated['category_id'] ?? null,
                $validated
            ));
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to search products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
