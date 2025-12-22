<?php
namespace App\Repositories;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentRepository implements PaymentRepositoryInterface
{

    /**
     * @param mixed $request
     */
    public function all($request, $userId)
    {
        $user = User::findOrFail($userId);

        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        if (in_array('Contractor', $user->roles->pluck('name', 'id')->toArray())) {
            return PaymentResource::collection(
                Payment::when(request('search'), function ($query) {
                    $query->where('model', 'like', '%' . request('search') . '%')
                          ->orWhere('model_id', 'like', '%'. request('search') . '%');
                })->where('user_id', $userId)->orderBy($field, $order)->paginate($perPage)
            );
        }
        return PaymentResource::collection(
            Payment::when(request('search'), function ($query) {
                $query->where('model', 'like', '%' . request('search') . '%')
                      ->orWhere('model_id', 'like', '%'. request('search') . '%');
            })->orderBy($field, $order)->paginate($perPage)
        );
    }


    public function create($data)
    {
        $api = new Api(config('app.razorpay_api_key'), config('app.seceret_key'));
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($data->input('razorpay_payment_id'));
        if (!empty($payment) && $payment['status'] == 'captured') {
            $paymentId = $payment['id'];
            $amount = $payment['amount'];
            $currency = $payment['currency'];
            $status = $payment['status'];
            $entity = $payment['entity'];
            $orderId = $payment['order_id'];
            $invoiceId = $payment['invoice_id'];
            $method = $payment['method'];
            $bank = $payment['bank'];
            $wallet = $payment['wallet'];
            $bankTranstionId = isset($payment['acquirer_data']['bank_transaction_id']) ? $payment['acquirer_data']['bank_transaction_id'] : '';
        } else {
            return redirect()->back()->with('error', 'Something went wrong, Please try again later!');
        }
        try {
            // Payment detail save in database
            $payment = new Payment;
            $payment->transaction_id = $paymentId;
            $payment->amount = $amount / 100;
            $payment->currency = $currency;
            $payment->entity = $entity;
            $payment->status = $status;
            $payment->order_id = $orderId;
            $payment->method = $method;
            $payment->bank = $bank;
            $payment->wallet = $wallet;
            $payment->bank_transaction_id = $bankTranstionId;
            $saved = $payment->save();
        } catch (Exception $e) {
            $saved = false;
        }
        if ($saved) {
            return redirect()->back()->with('success', __('Payment Detail store successfully!'));
        } else {
            return back()->withInput()->with('error', __('Something went wrong, Please try again later!'));
        }
    }

    public function update($data, $payment)
    {
        $payment->save($data->toArray());
        return $payment;
    }

    public function orderIdGenerate($request){

		$api = new Api(config('app.razorpay_api_key'), config('app.secret_key'));
        $order = $api->order->create([
            'receipt' => 'order_rcptid_11',
            'amount' => $request->amount * 100,
            'currency' => 'INR',
        ]); // Creates order
        return response()->json(['order_id' => $order['id']]);

	}

    public function verify_payment($request)
    {
        $success = true;
        $error = "Payment Failed!";
        if (empty($request->razorpay_payment_id) === false) {
            $api = new Api(config("app.razorpay_api_key"), config("app.secret_key"));
            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                $api->utility->verifyPaymentSignature($attributes);

            } catch (SignatureVerificationError $e) {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }
        $paymentResponse = $api->payment->fetch($request->razorpay_payment_id);

        if ($success === true) {
            // Update database with success data
            // Redirect to success page

            $input = [
                'user_id' => $request->user_id ? $request->user_id: null,
                'model' => $request->model ? $request->model: null,
                'model_id' => $request->model_id ? $request->model_id : null,
                'status' => $paymentResponse->status,
                'transaction_id' => $paymentResponse->id,
                'amount' => $paymentResponse->amount / 100,
                'currency' => $paymentResponse->currency,
                'order_id' => $paymentResponse->order_id,
                'method' => $paymentResponse->method,
                'amount_refunded' => $paymentResponse->amount_refunded,
                'bank' => $paymentResponse->bank,
                'wallet' => $paymentResponse->wallet,
                'entity' => isset($paymentResponse->card) ? $paymentResponse->card->entity: null,
                'bank_transaction_id' => isset($paymentResponse->acquirer_data->bank_transaction_id) ? $paymentResponse->acquirer_data->bank_transaction_id : '',
            ];
            $payment = Payment::create($input);
            $response = [
                'payment_id' => $payment->id,
                'success' => $success
            ];
        } else {
            // Update database with error data
            // Redirect to payment page with error
            // Pass $error along with route
            $response = [
                'error' => $error,
                'success' => $success
            ];
        }
        return $response;
    }

}

