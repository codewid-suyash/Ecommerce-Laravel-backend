<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {
        //condition to check cart is not empty
        if (!empty($request->cart)) {
            $order = new Order();
            $order->name = $request->name;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->mobile = $request->mobile;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->grand_total = $request->grand_total;
            $order->subtotal = $request->subtotal;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->payment_status = $request->payment_status;
            $order->status = $request->status;
            $order->user_id = $request->user()->id;
            $order->save();

            // Save order items
            foreach ($request->cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->price = $item['qty'] * $item['price'];
                $orderItem->unit_price = $item['price'];
                $orderItem->qty = $item['qty'];
                $orderItem->product_id = $item['product_id'];
                $orderItem->size = $item['size'];
                $orderItem->name = $item['title'];
                $orderItem->order_id = $order->id;
                $orderItem->save();
            }
            return response()->json([
                'message' => 'Order placed successfully',
                'id' => $order->id,
                'status' => '200'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Cart is empty',
                'status' => '400'
            ], 400);
        }
    }
}
