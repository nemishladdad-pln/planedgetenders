<?php
namespace App\Repositories\Interfaces;

Interface ProjectRepositoryInterface {

    public function all($request, $userId);

    public function create($data);

    public function update($data, $project);
}
