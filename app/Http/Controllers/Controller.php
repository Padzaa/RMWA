<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
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
        $orderColumn = $order[0] ?? self::DEFAULT_ORDER_COLUMN;
        $orderDirection = $order[1] ?? self::DEFAULT_ORDER_DIRECTION;
        return $query->orderBy($orderColumn, $orderDirection);
    }

    /**
     * Paginate the given query.
     */
    protected function paginate($query, $request = null)
    {
        $per_page = isset($request->per_page) ?? 10;
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

    /**
     * Reconstructing the data to be adapted for displaying charts
     * @param $data
     * @return mixed
     */
    protected function reconstructDataForMonthlyCharts($data): mixed
    {
        for ($i = 1; $i <= 12; $i++) {
            $inf = $data->firstWhere('Month', $i);
            if (!$inf) {
                // If the user for the current month doesn't exist, create a new one.
                $inf = new BaseModel();
                $inf->Month = $i;
                $inf->Count = 0;
                $data->push($inf);
            }

        }
        return $data->sortBy('Month')->pluck('Count');

    }

    /**
     * Make recipients for notifications
     */
    protected function finalRecipients($users_id)
    {
        $users_id = gettype($users_id) == "array" ? $users_id : [$users_id];
        $admins = \App\Models\User::getAdmins()->get();
        $users = \App\Models\User::whereIn("id", $users_id)->get();

        return collect()->merge($users)->merge($admins)->unique();
    }

}
