<?php
namespace App\Repositories\Interfaces;

Interface SettingRepositoryInterface {

    public function all();

    public function create($data);

    public function update($data, $setting);
}
