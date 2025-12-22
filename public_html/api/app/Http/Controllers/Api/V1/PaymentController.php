<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        return $this->paymentService->all($request, $userId);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $payment = $this->paymentService->create($request);
        if (!$payment) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Payment done Successfully', 'data' => $payment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return PaymentResource::make(Payment::findOrFail($payment->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment = $this->paymentService->update($request, $payment);
        if (!$payment) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Payment updated Successfully', 'data' => $payment], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    public function generate_order_id(Request $request)
    {
        $orderId = $this->paymentService->generate_order_id($request);
        return response()->json(['message' => 'Payment updated Successfully', 'data' => $orderId], 201);
    }

    public function verify_payment(Request $request)
    {
        $response = $this->paymentService->verify_payment($request);
        return response()->json(['data' => $response], 201);
    }
}
