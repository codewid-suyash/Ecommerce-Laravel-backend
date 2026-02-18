<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function getShipping()
    {
        $shipping = ShippingCharge::first();
        return response()->json([
            'data' => $shipping,
            'status' => 200
        ], 200);
    }

    public function updateShipping(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_charge' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        ShippingCharge::updateOrInsert(
            ['id' => 1],
            ['shipping_charge' => $request->shipping_charge]
        );
        // $shipping = ShippingCharge::find(1);
        // if ($shipping == null) {
        //     $model = new ShippingCharge();
        //     $model->shipping_charge = $request->shipping_charge;
        //     $model->save();
        // }
        // else{
        //     $shipping->shipping_charge = $request->shipping_charge;
        //     $shipping->save();
        // }
        return response()->json([
            'message' => 'Shipping charge saved successfully',
            'status' => 200
        ], 200);
    }
}
