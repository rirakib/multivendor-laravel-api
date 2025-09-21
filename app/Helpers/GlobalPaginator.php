<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalPaginator
{
    /**
     * Paginate any collection.
     *
     * @param Collection $collection
     * @param int|null $perPage
     * @param int|null $page
     * @return LengthAwarePaginator
     */
    public static function paginateCollection(Collection $collection, $perPage = null, $page = null)
    {
        $page = $page ?: (int) request()->get('page', 1);
        $perPage = $perPage ?: (int) request()->get('per_page', 20);

        $total = $collection->count();
        $items = $collection->forPage($page, $perPage)->values(); // slice collection

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

    /**
     * Return array format for frontend JSON
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    public static function format(LengthAwarePaginator $paginator)
    {
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

    /**
     * Convenient method to paginate collection and return frontend-ready array
     *
     * @param Collection $collection
     * @param int|null $perPage
     * @param int|null $page
     * @return array
     */
    public static function paginate(Collection $collection, $perPage = null, $page = null)
    {
        $paginator = self::paginateCollection($collection, $perPage, $page);
        return self::format($paginator);
    }
}
