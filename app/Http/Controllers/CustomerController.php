<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer as StripeCustomer;
use Stripe\Exception\ApiErrorException;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'name'  => 'required|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            $stripeCustomer = StripeCustomer::create([
                'email' => $request->email,
                'name'  => $request->name
            ]);

            return ok('Customer Create Successfully', $stripeCustomer);
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }

    public function update(Request $request, $stripe_customer_id)
    {

        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'name'  => 'required|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            $stripeCustomer = StripeCustomer::update($stripe_customer_id, [
                'email' => $request->email,
                'name'  => $request->name
            ]);

            return ok('Customer Updated Successfully', $stripeCustomer);
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }

    public function delete($stripe_customer_id)
    {
        try {
            $stripeCustomer = StripeCustomer::retrieve($stripe_customer_id);
            $stripeCustomer->delete();
            return ok('Customer Deleted');
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }
}
