<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Price;

class ItemController extends Controller
{
    /*
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'product_id'    => 'required|integer',
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'currency'      => 'nullable|string',
            'price'         => 'required|integer',
            'stripe_id'     => 'nullable|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        // create price for the item
        $price = Price::create([
            'unit_amount'   => $request->price,
            'currency'      => $request->currency ?? 'usd',
            'product'       => $request->product_id,
        ]);

        return ok('Item Created', $price);
    }
    */
}
