<?php
namespace App\Services\Setting;

use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class SettingService
{

    public function __construct(protected SettingRepositoryInterface $settingRepository) { }

    public function all()
    {
        return $this->settingRepository->all();
    }


    public function create(StoreSettingRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->settingRepository->create($request);
    }


    public function update(UpdateSettingRequest $request, Setting $setting): mixed
    {
        // if (!$request->validated()) {
        //     return false;
        // }

        return $this->settingRepository->update($request, $setting);
    }
}
