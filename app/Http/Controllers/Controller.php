<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BasController;
use Inertia\Inertia;

class Controller extends BasController
{
    use AuthorizesRequests, ValidatesRequests;

    const DEFAULT_ORDER_COLUMN = "created_at";
    const DEFAULT_ORDER_DIRECTION = "desc";
    protected function orderBy($query,$request){
        $order = explode("-",$request->order);
        $orderColumn = $order[0] ?? self::DEFAULT_ORDER_COLUMN;
        $orderDirection = $order[1] ?? self::DEFAULT_ORDER_DIRECTION;
        return $query->orderBy($orderColumn,$orderDirection);
    }

    protected function paginate($query,$request){
        $per_page = $request->per_page ?? 10;
        return $query->paginate($per_page);
    }
}
