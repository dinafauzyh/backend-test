<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Validator;

class CartController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get logged user
        $user = auth()->user()->id;
        //get all cart data
        $carts = Cart::with('product')->where('user_id', $user)->get();
        if ($carts->isEmpty()){
            return $this->error('Your cart is empty!', 404);
        } else {
            return $this->success($carts, 'Carts data retrieved successfully');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CartRequest $request)
    {
        try {
            //check product data
            $product = Product::where('id', $request->product_id)->first();
            if (!$product) {
                return $this->error('Product not found!', 404);
            }
            //get logged user for check user's cart
            $user = auth()->user()->id;
            $cart = Cart::where('user_id', $user)->where('product_id', $request->product_id)->first();
            
            if (!$cart) {
                $cart = new Cart();
                $cart->qty = $request->qty;
            } else {
                $cart->qty += $request->qty;
            }

            if ($cart->qty > $product->stock) {
                $cart->qty = $product->stock;
            }

            //save to database
            $cart->user_id = $user;
            $cart->product_id = $request->product_id;

            if (!$cart->save()) {
                return $this->error('Failed to save data!', 400);
            } else {
                return $this->success($cart, 'Product successfully added to cart');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('Failed to save data!', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $cart)
    {
        try {
            //find cart item
            $cartItem = Cart::with('product')->where('id', $cart)->where('user_id', auth()->user()->id)->first();

            //check user request
            $validator = Validator::make($request->all(), [
                'qty' => 'required|numeric',
            ]);

            //response validator failed
            if ($validator->fails()) {
                return $this->error($validator->errors(), 400);
            }

            if (!$cartItem) {
                return $this->error('Cart item not found!', 404);
            } else {
                $cartItem->qty = $request->qty;
                if ($cartItem->qty > $cartItem->product->stock) {
                    $cartItem->qty = $cartItem->product->stock;
                }
            }

            if (!$cartItem->save()) {
                return $this->error('Failed to save data!', 400);
            } else {
                return $this->success($cartItem, 'Cart item successfully updated');
            }
        } catch (\Throwable $th) {
            // throw $th;
            return $this->error('Failed to save data!', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy($cart =null)
    {
        try {
            if ($cart != null) {
                //destroy cart item by given id and logged user id
                $cartItem = Cart::where('id', $cart)->where('user_id', auth()->user()->id)->first();

                if ($cartItem) {
                    $cartItem->delete();
                    return $this->success($cartItem, 'Cart item successfully deleted');
                } else {
                    return $this->error('Cart item not found!', 404);
                }
            } else {
                $cartData = Cart::where('user_id', auth()->user()->id)->get();
                
                if (!$cartData->isEmpty()) {
                    $cartData->each->delete();
                    return $this->success($cartData, 'All cart item successfully deleted');
                } else {
                    return $this->error('No data found!', 404);
                }
            }      
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('Failed to delete data!', 500);
        }   
    }
}
