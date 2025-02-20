<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all(array $options = [])
    {
        $perPage = $this->resolvePerPage($options);
        $page = $this->resolvePage($options);
        $sortBy = $options['sort_by'] ?? 'created_at';
        $sortDirection = $options['sort_direction'] ?? 'desc';

        return $this->model->with('category')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($options);
    }

    public function find($id)
    {
        return $this->model->with('category')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = $this->find($id);
        $product->delete();
    }

    public function search($search = null, $categoryId = null, array $options = [])
    {
        $query = $this->model->with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $perPage = $this->resolvePerPage($options);
        $page = $this->resolvePage($options);
        $sortBy = $options['sort_by'] ?? 'created_at';
        $sortDirection = $options['sort_direction'] ?? 'desc';

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($options);
    }

    public function getPaginationStats()
    {
        $perPage = $this->resolvePerPage([]);
        $page = $this->resolvePage([]);
        $total = $this->model->count();

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }
}