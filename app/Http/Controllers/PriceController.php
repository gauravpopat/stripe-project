<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;
use Stripe\Price as StripePrice;


class PriceController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'item_id'       => 'required|string',
            'product_id'    => 'required|string',
            'plan_id'       => 'required|string',
            'amount'        => 'required|integer|min:1',
            'currency'      => 'nullable|string',
            'interval'      => 'required|string|in:day,week,month,year'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            $stripePrice = StripePrice::create([
                'product'       => $request->product_id,
                'unit_amount'   => $request->amount,
                'currency'      => $request->currency ?? 'usd',
                'recurring'     => [
                    'interval'  => $request->interval,
                ],
                'metadata'      => [
                    'item_id'   => $request->item_id,
                    'plan_id'   => $request->plan_id
                ]
            ]);

            return ok('Price Created', $stripePrice);
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }
}
