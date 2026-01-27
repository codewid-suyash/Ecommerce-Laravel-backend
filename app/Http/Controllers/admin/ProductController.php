<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->with(['product_images', 'product_sizes'])->get();
        return response()->json([
            'data' => $products,
            'status' => 200,
        ], 200);
    }

    public function show($id)
    {
        $product = Product::with(['product_images', 'product_sizes'])->find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product Not Found',
                'status' => 404,
            ], 404);
        }
        $productSizes= $product->product_sizes()->pluck('size_id')->toArray();
        return response()->json([
            'data' => $product,
            'status' => 200,
            'productSizes' => $productSizes
        ], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category' => 'required|integer',
            'price' => 'required|numeric',
            'status' => 'required',
            'sku' => 'required|unique:products,sku',
            'is_featured' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        $product = new Product();
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->category_id = $request->category;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->qty = $request->qty;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->save();

        //save product sizes
        if(!empty($request->sizes)){
            foreach($request->sizes as $sizeId){
                $productSize = new ProductSize();
                $productSize->product_id = $product->id;
                $productSize->size_id = $sizeId;
                $productSize->save();
            }
        }

        //save product gallery
        if(!empty($request->gallery)){
            foreach($request->gallery as $key=> $TempImageId){
                $tempImage= TempImage::find($TempImageId);

                //large image
                $extArray = explode('.',$tempImage->name);
                $ext = end($extArray);
                $rand = rand(1000,10000);
                $imageName = $product->id.'-'.$rand.time().'.'.$ext;
                $manager= new ImageManager(Driver::class);
                $img = $manager->read(public_path('uploads/temp/'.$tempImage->name));
                $img->scaleDown(1200);
                $img->save(public_path('uploads/products/large/'.$imageName));

                //small image
                 $manager= new ImageManager(Driver::class);
                $img = $manager->read(public_path('uploads/temp/'.$tempImage->name));
                $img->coverDown(400,460);
                $img->save(public_path('uploads/products/small/'.$imageName));

                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = $imageName;
                $productImage->save();

                //save to product gallery
                if($key==0){
                    $product->image = $imageName;
                    $product->save();
                }
            }
        }


        return response()->json([
            'message' => 'Product Created Successfully',
            'status' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product Not Found',
                'status' => 404,
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category' => 'required|integer',
            'price' => 'required|numeric',
            'status' => 'required',
            'sku' => 'required|unique:products,sku,' . $id,
            'id',
            'is_featured' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->category_id = $request->category;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->qty = $request->qty;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->save();

        if(!empty($request->sizes)){
            ProductSize::where('product_id', $product->id)->delete();
            foreach($request->sizes as $sizeId){
                $productSize = new ProductSize();
                $productSize->product_id = $product->id;
                $productSize->size_id = $sizeId;
                $productSize->save();
            }
        }

        return response()->json([
            'message' => 'Product Updated Successfully',
            'status' => 200,
        ], 200);
    }

    public function destroy($id) {
        $product = Product::with('product_images')->find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product Not Found',
                'status' => 404,
            ], 404);
        }

        if($product->product_images){
            foreach($product->product_images as $productImage)
            {
                File::delete(public_path('uploads/products/large/'.$productImage->image));
                File::delete(public_path('uploads/products/small/'.$productImage->image));
            }
        }


        $product->delete();
        return response()->json([
            'message' => 'Product Deleted Successfully',
            'status' => 200,
        ], 200);
    }

    public function saveProductImage(Request $request ){

    $validator = Validator::make($request->all(), [
        'image' => 'required|image|mimes:jpeg,png,jpg,gif',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'status' => 400,
        ], 400);
    }

    $image = $request->file('image');
    $imageName = $request->product_id.'-'.time().'.'.$image->extension();

    //large image
    $manager= new ImageManager(Driver::class);
    $img = $manager->read($image->getPathName());
    $img->scaleDown(1200);
    $img->save(public_path('uploads/products/large/'.$imageName));

    //small image
    $manager= new ImageManager(Driver::class);
    $img = $manager->read($image->getPathName());
    $img->coverDown(400,460);
    $img->save(public_path('uploads/products/small/'.$imageName));
    //store the image
    $productImage = new ProductImage();
    $productImage->product_id = $request->product_id;
    $productImage->image = $imageName;
    $productImage->save();

    return response()->json([
        'data' => $productImage,
        'message' => 'Image uploaded successfully',
        'status' => 200,
    ], 200);
    }

    public function UpdateDefaultImage(Request $request){
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'message' => 'Product Not Found',
                'status' => 404,
            ], 404);
        }
        $product->image = $request->image;
        $product->save();

        return response()->json([
            'message' => 'Default Image Set Successfully',
            'status' => 200,
        ], 200);
    }

    public function deleteProductImage($id){
        $productImage = ProductImage::find($id);
        if (!$productImage) {
            return response()->json([
                'message' => 'Product Image Not Found',
                'status' => 404,
            ], 404);
        }
        //delete image files

        File::delete(public_path('uploads/products/large/'.$productImage->image));
        File::delete(public_path('uploads/products/small/'.$productImage->image));
        $productImage->delete();

        return response()->json([
            'message' => 'Product Image Deleted Successfully',
            'status' => 200,
        ], 200);
    }
}
