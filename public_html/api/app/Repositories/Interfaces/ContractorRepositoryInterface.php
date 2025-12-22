<?php
namespace App\Repositories\Interfaces;

Interface ContractorRepositoryInterface {

    public function all($request, $userId=null);

    public function create($data);

    public function show($data);

    public function update($data, $contractor);

    public function delete($contractor);

    public function findByUser($userId);
}
