<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer as StripeCustomer;
use App\Models\Customer;
use Exception;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function addCard(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required|exists:customers,id',
            'card_number'   => 'required|numeric',
            'exp_month'     => 'required|digits:2|numeric',
            'exp_year'      => 'required|digits:4|numeric',
            'cvc'           => 'required|digits_between:3,4|numeric'
        ]);

        if ($validation->fails()) {
            return error('Validation Error', $validation->errors(), 'validation');
        }

        try {
            $customer = Customer::find($request->customer_id);

            $stripeCustomer = StripeCustomer::retrieve($customer->stripe_id);

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
        

            return ok('Card Added.');
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return error('Stripe API Error', $e->getMessage(), 'stripe_api');
        } catch (\Exception $e) {
            return error('Unknown Error', $e->getMessage(), 'unknown');
        }
    }

    public function createIntent(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required|exists:customers,id',
            'amount'        => 'required|numeric',
            'currency'      => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            $customer = Customer::find($request->customer_id);
            $stripeCustomer = StripeCustomer::retrieve($customer->stripe_id);

            $paymentIntent = PaymentIntent::create([
                'amount'        => $request->amount,
                'currency'      => $request->currency,
                'customer'      => $stripeCustomer->id,
                'description'   => 'Sample Intent',
            ]);

            return ok('Payment Intent Created Successful', $paymentIntent);
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        }
    }
}
