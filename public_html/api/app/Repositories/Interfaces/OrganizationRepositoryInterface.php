<?php
namespace App\Repositories\Interfaces;

Interface OrganizationRepositoryInterface {

    public function all($request);

    public function create($data);

    public function update($data, $organization);

    public function delete($organization);

    public function findByUser($userId);

}
