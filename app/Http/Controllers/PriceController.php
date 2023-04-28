<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Price as StripePrice;
use App\Models\Product;
use App\Models\Price;


class PriceController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'item_id'       => 'required|exists:items,id',
            'product_id'    => 'required|exists:products,stripe_product_id',
            'plan_id'       => 'required|exists:plans,id',
            'amount'        => 'required|integer|min:1',
            'currency'      => 'nullable|string',
            'interval'      => 'required|string|in:day,week,month,year'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $stripePrice = StripePrice::create([
            'product'       => $request->product_id,
            'unit_amount'   => $request->amount,
            'currency'      => $request->currency ?? 'usd',
            'recurring'     => [
                'interval'  => $request->interval,
            ]
        ]);

        $price = Price::where('stripe_id', $stripePrice->id)->first();
        $product = Product::where('stripe_product_id', $request->product_id)->first();

        if (!$price) {
            $price = Price::create([
                'product_id' => $product->id,
                'plan_id' => $request->plan_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'stripe_id' => $stripePrice->id,
            ]);
        }
        
        $item = Item::find($request->item_id);
        $item->prices()->attach($price->id);

        $plan = Plan::find($request->plan_id);
        $plan->prices()->attach($price->id);

        return ok('Price Created', $price);
    }
}
