<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected $defaultPerPage = 10;
    protected $maxPerPage = 100;

    public function resolvePage(array $options)
    {
        $page = $options['page'] ?? request()->get('page', 1);
        return max(1, (int) $page);
    }

    public function resolvePerPage(array $options)
    {
        $perPage = $options['per_page'] ?? request()->get('per_page', $this->defaultPerPage);
        return min(max(1, (int) $perPage), $this->maxPerPage);
    }

    protected function customPaginate($items, $total, $perPage, $page)
    {
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
