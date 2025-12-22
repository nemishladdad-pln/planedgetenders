<?php
namespace App\Repositories\Interfaces;

Interface ProfileUserRepositoryInterface {

    public function all($request);

    public function create($data);

    public function update($data, $profileUser);

}