<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer as StripeCustomer;
use Stripe\Stripe;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|unique:customers,email',
            'name'  => 'required|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $stripeCustomer = StripeCustomer::create([
            'email' => $request->email,
            'name'  => $request->name
        ]);

        Customer::create($request->only(['email', 'name']) + [
            'stripe_id' => $stripeCustomer->id
        ]);

        return response()->json([
            'message'   => 'Customer Created Successfully'
        ]);
    }

    public function update(Request $request, $customerId)
    {
        $customer = Customer::where('stripe_id', $customerId)->first();


        $validation = Validator::make($request->all(), [
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'name'  => 'required|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        StripeCustomer::update($customerId, [
            'email' => $request->email,
            'name'  => $request->name
        ]);

        $customer->update($request->only(['name', 'email']));

        return ok('Customer Updated Successfully', $customer);
    }

    public function delete($customerId)
    {
        $customer = Customer::where('stripe_id', $customerId)->first();
        $stripeCustomer = StripeCustomer::retrieve($customer->stripe_id);
        $stripeCustomer->delete();
        $customer->delete();

        return ok('Customer Deleted');
    }
}
