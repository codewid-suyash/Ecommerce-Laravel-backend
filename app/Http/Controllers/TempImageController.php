<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        //store the image
        $tempImage = new TempImage();
        $tempImage->name = "dummy img";
        $tempImage->save();

        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('uploads/temp'), $imageName);

        $tempImage->name = $imageName;
        $tempImage->save();

        //save image thumbnail using intervention
        $manager = new ImageManager(Driver::class);
        $img = $manager->read(public_path('uploads/temp/' . $imageName));
        $img->coverDown(400, 450);
        $img->save(public_path('uploads/temp/thumb/' . $imageName));

        return response()->json([
            'data' => $tempImage,
            'message' => 'Image uploaded successfully',
            'status' => 200,
        ], 200);
    }
}
