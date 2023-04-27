<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Product as StripeProduct;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'required|url'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $stripeProduct = StripeProduct::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'images'        => [$request->image]
        ]);

        $product = Product::create($request->only(['name', 'description', 'image']) + [
            'stripe_product_id' => $stripeProduct->id
        ]);

        return ok('Product Created', $product);
    }


    public function update($productId, Request $request)
    {
        $product = Product::where('stripe_product_id', $productId)->first();

        $validation = Validator::make($request->all(), [
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'required|url'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        StripeProduct::update($product->stripe_product_id, [
            'name'          => $request->name,
            'description'   => $request->description,
            'images'        => [$request->image]
        ]);

        $product->update($request->only(['name', 'description', 'image']));

        return ok('Product Updated Successfull.');
    }

    public function delete($productId, Request $request)
    {
        $product = Product::where('stripe_product_id', $productId)->first();
        $stripeProduct = StripeProduct::retrieve($product->stripe_product_id);
        $stripeProduct->delete();
        $product->delete();
        return ok('Product Deleted');
    }
}
