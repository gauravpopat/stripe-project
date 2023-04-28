<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Price;

class ItemController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'product_id'    => 'required|integer|exists:products,id',
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'currency'      => 'nullable|string',
            'price'         => 'required|integer',
            'stripe_id'     => 'nullable|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $product = Product::find($request->product_id);

        // create price for the item
        $price = Price::create([
            'unit_amount'   => $request->price,
            'currency'      => 'usd',
            'product'       => $product->stripe_product_id,
        ]);

        // Create the item with the given attributes and the price ID
        $item = Item::create($request->only(['product_id', 'name', 'description']) + [
            'price_id'  => $price->id,
            'currency'  => 'usd',
            'stripe_id' => $price->id
        ]);

        return ok('Item Created', $item);
    }
}
