<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Helpers\FileHelper;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        
        if ($products->isEmpty()) {
            return $this->error('Products data not found', 404);
        } else {
            return $this->success($products, 'Products data retrieved successfully');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            $fileImages = $request->file('image');
            if ($fileImages) {
                $image = (new FileHelper())->moveFile($fileImages, 'product-images/');
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'brand_name' => $request->brand_name,
                'image' => $image,
            ]);

            return $this->success($product, 'Product created successfully');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('Product creation failed', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
        $productData = Product::find($product);

        if ($productData) {
            return $this->success($productData, 'Product data retrieved successfully');
        } else {
            return $this->error('Product data not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $product)
    {
        try {
            $productData = Product::find($product);

            if (!$productData) {
                return $this->error('Product not found', 404);
            } else {

                if (isset($request->image)) {
                    $fileImages = $request->file('image');

                    if($fileImages) {
                        $image = (new FileHelper())->moveFile($fileImages, 'product-images/');
                        unlink($productData->image);
                    } else {
                        $image = $productData->image;
                    }

                    $productData->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'price' => $request->price,
                        'stock' => $request->stock,
                        'brand_name' => $request->brand_name,
                        'image' => $image,
                    ]);

                    return $this->success($productData, 'Product updated successfully');
                }

            }
        } catch (\Throwable $th) {
            // throw $th;
            return $this->error('Product update failed', 500);
        }
            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($product)
    {
        try {
            $productData = Product::find($product);

            if ($productData) {
                $productData->delete();
                return $this->success($productData, 'Product deleted successfully');
            } else {
                return $this->error('Product not found', 404);
            }
        } catch (\Throwable $th) {
            // throw $th;
            return $this->error('Product deletion failed', 500);
        }
    }
}
