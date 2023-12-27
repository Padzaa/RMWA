<?php

namespace App\Http\Controllers;

use http\Client\Curl\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    const DEFAULT_ORDER_COLUMN = "created_at";
    const DEFAULT_ORDER_DIRECTION = "desc";


    /**
     * Generates the function comment for the given function body.
     */
    protected function orderBy($query, $request = null)
    {
        if (isset($request->order)) {
            $order = explode("-", $request->order);
        }
        $order_column = $order[0] ?? self::DEFAULT_ORDER_COLUMN;
        $order_direction = $order[1] ?? self::DEFAULT_ORDER_DIRECTION;
        return $query->orderBy($order_column, $order_direction);
    }

    /**
     * Paginate the given query.
     */
    protected function paginate($query, $request = null)
    {
        $per_page = isset($request->per_page) ? $request->per_page : 10;
        return $query->paginate($per_page);
    }

    /**
     * Order and paginate the query result.
     */
    protected function orderAndPaginate($query, $request)
    {
        $query = $this->orderBy($query, $request);
        return $this->paginate($query, $request);
    }

    /**
     * Sending a success message to user interface
     */
    protected function flashSuccessMessage($message): void
    {
        session()->flash('alert', [
            'title' => 'Success!',
            'message' => $message,
            'type' => 'success'
        ]);
    }

    /**
     * Sending an error message to user interface
     */
    protected function flashErrorMessage($message): void
    {
        session()->flash('alert', [
            'title' => 'Error!',
            'message' => $message,
            'type' => 'error'
        ]);
    }

}
