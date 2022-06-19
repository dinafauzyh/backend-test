<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Product;
use App\Models\Expedition;
use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all transactions data
        $transcations = Transaction::with(['user', 'address', 'bank', 'expedition', 'transaction_details', 'transaction_details.product'])->get();

        if ($transcations->isEmpty()) {
            return $this->error('No data found', 404);
        } else {
            return $this->success($transcations, 'Transcations data retrieved successfully');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        //get user id
        $userId = auth()->user()->id;
        //check address
        $address = Address::where('user_id', $userId)->where('is_default', true)->first();
        if (!$address) {
            return $this->error('Default address not found. Please add your address first', 404);
        }

        //check bank
        $bank = Bank::find($request->bank_id);
        if (!$bank) {
            return $this->error('Bank not found', 404);
        }

        //check expedition
        $expedition = Expedition::find($request->expedition_id);
        if (!$expedition) {
            return $this->error('Expedition not found', 404);
        }

        //create transaction
        DB::beginTransaction();
        $transaction = Transaction::create([
            'address_id' => $address->id,
            'bank_id' => $request->bank_id,
            'expedition_id' => $request->expedition_id,
            'delivery_fee' => $request->delivery_fee,
            'user_id' => $userId,
            'status' => 'pending'
        ]);

        if (!$transaction) {
            DB::rollBack();
            return $this->error('Failed to save transaction', 500);
        }

        //check product, calculation the total
        $itemToCheckout = $request->products;
        $totalBeforeDeliveryFee = 0;

        foreach ($itemToCheckout as $item) {
            $product = Product::where('id', $item['product_id'])->where('stock', '>', 0)->first();
            if (!$product) {
                return $this->error('Product not found or out of stock', 404);
            }

            $totalPerItem = $product->price * $item['qty'];
            $totalBeforeDeliveryFee += $totalPerItem;

            //create transaction detail
            $transactionDetails = $transaction->transaction_details()->create([
                'qty' => $item['qty'],
                'price' => $product->price,
                'total' => $totalPerItem,
                'product_id' => $item['product_id']
            ]);

            if (!$transactionDetails) {
                DB::rollBack();
                return $this->error('Failed to save detail transaction', 500);
            }

            //update stock
            $product->stock -= $item['qty'];
            if (!$product->save()) {
                DB::rollBack();
                return $this->error('Failed to update stock', 500);
            }

            //delete data cart
            $cart = Cart::where('product_id', $item['product_id'])->where('user_id', $userId)->first();
            if (!is_null($cart)) {
                if (!$cart->delete()) {
                    DB::rollBack();
                    return $this->error('Failed to delete cart', 500);
                }
            } 
        }
        
        //update total
        $transaction->total = $totalBeforeDeliveryFee + $request->delivery_fee;
        if (!$transaction->save()) {
            DB::rollBack();
            return $this->error('Failed to update total transaction', 500);
        }

        DB::commit();
        return $this->success($transaction, 'Transaction created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show($transaction)
    {
        $transactionDetail = Transaction::with(
            ['user', 'address', 'bank', 
             'expedition', 'transaction_details', 'transaction_details.product'])->find($transaction);
        if (!$transactionDetail) {
            return $this->error('Transaction not found', 404);
        } else {
            return $this->success($transactionDetail, 'Transaction data retrieved successfully');
        }
    }
}
