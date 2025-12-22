<?php
namespace App\Repositories\Interfaces;

Interface PaymentRepositoryInterface {

    public function all($request, $user);

    public function create($data);

    public function update($data, $payment);

    public function orderIdGenerate($request);

    public function verify_payment($request);
}
