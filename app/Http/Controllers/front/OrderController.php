<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
            $order->payment_method = $request->payment_method;
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

    public function createPaymentIntent(Request $request)
    {
        try{
            if($request->amount > 0){
                Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => $request->amount,
                    'currency' => 'USD',
                    'payment_method_types' => ['card'],
                ]);

                return response()->json([
                    'message' => 'Payment Intent created successfully',
                    'status' => '200',
                    'clientSecret' => $paymentIntent->client_secret
                ], 200);

            }else{
                return response()->json([
                    'message' => 'Amount Must be greater than zero',
                    'status' => '400'
                ], 400);
            }

        }
        catch(\Exception $e){

        }


    }
}
