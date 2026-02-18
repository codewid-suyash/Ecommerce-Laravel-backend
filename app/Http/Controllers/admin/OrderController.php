<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'data' => $orders,
            'status' => 200

        ],200);
    }

    public function detail($id)
    {
        $order = Order::with(['items','items.product'])->findOrFail($id);
        return response()->json([
            'data' => $order,
            'status' => 200

        ],200);
    }

    public function updateOrder($id, Request $request)
    {
        $order = Order::findOrFail($id);

        if($order == 'null'){
            return response()->json([
                'message' => 'Order not found',
                'status' => 404

            ],status: 404);
        }

        $order->status = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $order,
            'status' => 200

        ],200);
    }
}
