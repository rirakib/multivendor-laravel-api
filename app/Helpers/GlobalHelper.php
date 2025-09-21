<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class GlobalHelper
{


    /**
     * Paginate any Eloquent query and return full pagination info.
     *
     * @param Builder $query
     * @param int|null $perPage
     * @return array
     */
    public static function paginate($query, $perPage = null)
    {
        $perPage = $perPage ?: (int) request()->get('per_page', 20);
        $page = (int) request()->get('page', 1);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'data' => $paginator->items(),
        ];
    }
}
