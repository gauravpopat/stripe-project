<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer as StripeCustomer;
use App\Models\Customer;
use App\Models\Price;
use App\Models\Product;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Price as StripePrice;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function addCard(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required',
            'card_number'   => 'required|numeric',
            'exp_month'     => 'required|digits:2|numeric',
            'exp_year'      => 'required|digits:4|numeric',
            'cvc'           => 'required|digits_between:3,4|numeric'
        ]);

        if ($validation->fails()) {
            return error('Validation Error', $validation->errors(), 'validation');
        }

        try {

            $stripeCustomer = StripeCustomer::retrieve($request->customer_id);

            $paymentMethod = PaymentMethod::create([
                'type'  => 'card',
                'card'  =>  [
                    'number'    => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'exp_year'  => $request->exp_year,
                    'cvc'       => $request->cvc
                ]
            ]);

            $paymentMethod->attach(['customer'  => $stripeCustomer->id]);

            $stripeCustomer->default_payment_method = $paymentMethod->id;

            return ok('Card Added.', $paymentMethod);
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return error('Stripe API Error', $e->getMessage(), 'stripe_api');
        } catch (\Exception $e) {
            return error('Unknown Error', $e->getMessage(), 'unknown');
        }
    }

    public function createIntent(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required',
            'amount'        => 'required|numeric',
            'currency'      => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            $stripeCustomer = StripeCustomer::retrieve($request->customer_id);

            $paymentIntent = PaymentIntent::create([
                'amount'        => $request->amount,
                'currency'      => $request->currency,
                'customer'      => $stripeCustomer->id,
                'description'   => 'Testing Intent',
            ]);

            return ok('Payment Intent Created Successful', $paymentIntent);
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        }
    }

    public function createCheckoutSession(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required|string',
            'price_id'      => 'required|string',
            'success_url'   => 'required|url',
            'cancel_url'    => 'required|url',
            'quantity'      => 'required|integer|min:1',
        ]);

        if ($validation->fails()) {
            return error('Validation Error', $validation->errors(), 'validation');
        }

        try {

            $stripeCustomer = StripeCustomer::retrieve($request->customer_id);
            $stripePrice = StripePrice::retrieve($request->price_id);

            if ($stripePrice->type !== 'one_time') {
                throw new Exception("Price not valid");
            }

            $session = Session::create([
                'customer'              => $stripeCustomer->id,
                'payment_method_types'  => ['card'],
                'line_items'            => [
                    [
                        'price' => $stripePrice->id,
                        'quantity' => $request->quantity ?? 1,
                    ],
                ],
                'mode'                  => 'payment',
                'success_url'           => $request->success_url,
                'cancel_url'            => $request->cancel_url,
            ]);

            return ok('Session Created Successfully', $session);
        } catch (ApiErrorException $e) {
            return error('Stripe API Error', $e->getMessage());
        } catch (\Exception $e) {
            return error('Error', $e->getMessage());
        }
    }
}
