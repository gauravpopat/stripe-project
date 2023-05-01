<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;
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

        try {
            $stripeProduct = StripeProduct::create([
                'name'          => $request->name,
                'description'   => $request->description,
                'images'        => [$request->image]
            ]);

            return ok('Product Created', $stripeProduct);
        } catch (ApiErrorException $ae) {
            return error('Strpe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }


    public function update($stripe_product_id, Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'required|url'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');
        try {
            StripeProduct::update($stripe_product_id, [
                'name'          => $request->name,
                'description'   => $request->description,
                'images'        => [$request->image]
            ]);

            return ok('Product Updated Successfull.');
        } catch (ApiErrorException $ae) {
            return error('Strpe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }

    public function delete($stripe_product_id)
    {
        try {
            $stripeProduct = StripeProduct::retrieve($stripe_product_id);
            $stripeProduct->delete();
            return ok('Product Deleted');
        } catch (ApiErrorException $ae) {
            return error('Strpe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }
}
