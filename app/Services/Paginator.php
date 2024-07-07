<?php

namespace App\Services;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator as ParentPaginator;

final class Paginator
{
    public function paginate(Request $request, Builder $builder): LengthAwarePaginator
    {
        $limit = $request->input('limit', 10);
        $sortBy = $request->input('sortBy', 'id');
        $page = $request->input('page', 1);
        $sort = 'ASC';
        if ($request->input('desc') === 'true') {
            $sort = 'DESC';
        }

        return $builder->orderBy($sortBy, $sort)->paginate($limit, ['*'], 'page', $page);
    }
}
