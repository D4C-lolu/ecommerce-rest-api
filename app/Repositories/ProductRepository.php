<?php

namespace App\Repositories;

class ProductRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all(array $options = [])
    {
        $perPage = $options['per_page'] ?? 10;
        $page = $options['page'] ?? 1;
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

        $perPage = $options['per_page'] ?? 10;
        $page = $options['page'] ?? 1;
        $sortBy = $options['sort_by'] ?? 'created_at';
        $sortDirection = $options['sort_direction'] ?? 'desc';

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($options);
    }
}
