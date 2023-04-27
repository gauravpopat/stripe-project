<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;

class PlanController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'          => 'required|string|unique:plans,name',
            'amount'        => 'required|numeric|min:0',
            'interval_unit' => 'required|in:day,week,month,year',
            'interval'      => 'required|numeric|min:1',
            'product_id'    => 'required|exists:products,id',
            'description'   => 'nullable|string'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $product = Product::find($request->product_id);

        $stripePlan = StripePlan::create([
            'nickname' => $request->name,
            'amount' => $request->amount,
            'currency' => 'usd',
            'interval' => $request->interval_unit,
            'interval_count' => $request->interval,
            'product' => $product->stripe_product_id
        ]);

        $plan = Plan::create($request->only(['name', 'amount', 'currency', 'interval', 'interval_unit', 'description']) + [
            'stripe_id' => $stripePlan->id
        ]);

        return ok('Plan Created', $plan);
    }

    public function update($plan_id, Request $request)
    {
        $plan = Plan::where('stripe_id', $plan_id)->first();

        $validation = Validator::make($request->all(), [
            'name'          => 'required|string|unique:plans,name,' . $plan->id,
            'amount'        => 'required|numeric|min:0'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        StripePlan::update($plan->stripe_id, [
            'nickname' => $request->name
        ]);

        $plan->update($request->only('name'));
        return ok('Plan Updated Successfull');
    }

    public function delete($plan_id)
    {
        $plan = Plan::where('stripe_id', $plan_id)->first();
        $stripePlan = StripePlan::retrieve($plan->stripe_id);
        $stripePlan->delete();
        $plan->delete();
        return ok('Plan Deleted Successfully');
    }
}
