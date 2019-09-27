<?php

namespace App\Http\Controllers;

use Common\Models\Orders;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *
     */
    public function getIndex(Request $request)
    {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit');

        $start = ($page - 1) * $limit;

        $data = [
            'total'       => 0,
            'orders'      => [],
        ];

        $orders = Orders::select([
            '*'
        ])
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Orders::count();

        if (!$orders->isEmpty()) {
            $data['user_group'] = $orders->toArray();
        }

        return $this->response(1, 'Success!', $data);
    }
}
