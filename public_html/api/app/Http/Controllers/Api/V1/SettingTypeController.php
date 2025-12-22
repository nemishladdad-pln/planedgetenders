<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SettingType;
use App\Http\Requests\StoreSettingTypeRequest;
use App\Http\Requests\UpdateSettingTypeRequest;

class SettingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SettingType $settingType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingTypeRequest $request, SettingType $settingType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SettingType $settingType)
    {
        //
    }
}
