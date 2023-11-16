<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BasController;

class Controller extends BasController
{
    use AuthorizesRequests, ValidatesRequests;

    const DEFAULT_ORDER_COLUMN = "created_at";
    const DEFAULT_ORDER_DIRECTION = "desc";

    /**
     * Generates the function comment for the given function body.
     */
    protected function orderBy($query, $request)
    {
        if ($request->order) {
            $order = explode("-", $request->order);
        }
        $orderColumn = $order[0] ?? self::DEFAULT_ORDER_COLUMN;
        $orderDirection = $order[1] ?? self::DEFAULT_ORDER_DIRECTION;
        return $query->orderBy($orderColumn, $orderDirection);
    }

    /**
     * Paginate the given query.
     */
    protected function paginate($query, $request)
    {
        $per_page = $request->per_page ?? 10;
        return $query->paginate($per_page);
    }

    /**
     * Order and paginate the query result.
     */
    protected function OrderAndPaginate($query, $request)
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
