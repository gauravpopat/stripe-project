<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDO;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Subscription;

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required|string',
            'price_id'      => 'required|string',
            'quantity'      => 'integer|min:1',
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {

            $subscription = Subscription::create([
                'customer'      => $request->customer_id,
                'items'         => [
                    [
                        'price'     => $request->price_id, // non one day price
                        'quantity'  => $request->quantity ?? 1
                    ]
                ]
            ]);
            return ok('Subscription Created', $subscription);
        } catch (ApiErrorException $ae) {
            return error('Strpe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }

    public function update($subscription_id, Request $request)
    {
        $validation = Validator::make($request->all(), [
            'cancel_at_period_end'  => 'required|in:true,false',
            'description'           => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());

        try {

            // return Subscription::retrieve($subscription_id);

            $subscription = Subscription::update(
                $subscription_id,
                [
                    'cancel_at_period_end'  => $request->cancel_at_period_end,
                    'metadata'              => [
                        'description'       => $request->description
                    ]
                ]
            );

            return ok('Subscription Updated Successfully', $subscription);
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }
}
