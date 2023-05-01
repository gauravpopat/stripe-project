<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;
use Stripe\Plan as StripePlan;

class PlanController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'                  => 'required|string',
            'amount'                => 'required|numeric|min:0',
            'interval_unit'         => 'required|in:day,week,month,year',
            'interval'              => 'required|numeric|min:1',
            'stripe_product_id'     => 'required',
            'description'           => 'required|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            $stripePlan = StripePlan::create([
                'nickname' => $request->name,
                'amount' => $request->amount,
                'currency' => 'usd',
                'interval' => $request->interval_unit,
                'interval_count' => $request->interval,
                'product' => $request->stripe_product_id
            ]);
            return ok('Plan Created', $stripePlan);
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }

    public function update($stripe_plan_id, Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'          => 'required|string',
            'amount'        => 'required|numeric|min:0'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        try {
            StripePlan::update($stripe_plan_id, [
                'nickname' => $request->name
            ]);

            return ok('Plan Updated Successfull');
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Stripe API Error', $e->getMessage());
        }
    }

    public function delete($stripe_plan_id)
    {
        try {
            $stripePlan = StripePlan::retrieve($stripe_plan_id);
            $stripePlan->delete();
            return ok('Plan Deleted Successfully');
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Stripe API Error', $e->getMessage());
        }
    }
}
