<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function all(array $options = []);
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    public function search($search = null, $categoryId = null, array $options = []);
    public function getPaginationStats();
    public function resolvePage(array $options);
    public function resolvePerPage(array $options);
}
