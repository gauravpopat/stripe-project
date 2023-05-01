<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;

class InvoiceController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'customer_id'   => 'required',
            'description'   => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());

        try {

            Invoice::create([
                'customer'      => $request->customer_id,
                'description'    => $request->description
            ]);

            return ok('Invoice Created.');
        } catch (ApiErrorException $ae) {
            return error('Stripe API Error', $ae->getMessage());
        } catch (Exception $e) {
            return error('Error', $e->getMessage());
        }
    }
}
