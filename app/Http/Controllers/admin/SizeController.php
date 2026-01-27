<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Symfony\Component\Mime\Message;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('id', 'ASC')->get();

        return response()->json([
            'data' => $sizes,
            'status' => 200,
        ],200);
    }
}
