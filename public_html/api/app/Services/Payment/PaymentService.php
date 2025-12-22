<?php
namespace App\Services\Payment;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\User;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\Request;

class PaymentService
{
    public function __construct(protected PaymentRepositoryInterface $paymentRepository) { }

    public function all(Request $request, $user)
    {
        return $this->paymentRepository->all($request, $user);
    }

    public function create(StorePaymentRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }
        return $this->paymentRepository->create($request);
    }

    public function update(UpdatePaymentRequest $request, $payment)
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->paymentRepository->update($request, $payment);
    }

    public function generate_order_id(Request $request)
    {
        return $this->paymentRepository->orderIdGenerate($request);
    }

    public function verify_payment(Request $request)
    {
        return $this->paymentRepository->verify_payment($request);
    }
}
