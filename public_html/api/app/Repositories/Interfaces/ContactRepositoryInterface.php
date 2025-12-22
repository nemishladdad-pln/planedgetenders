<?php
namespace App\Repositories\Interfaces;

Interface ContactRepositoryInterface {

    public function all($request);

    public function create($data);

}
