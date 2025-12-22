<?php
namespace App\Repositories\Interfaces;

Interface TenderRepositoryInterface {

    public function all($request, $userId = null, $status = null);

    public function create($data, $user = null);

    public function show($data);

    public function update($data, $tender);
}
