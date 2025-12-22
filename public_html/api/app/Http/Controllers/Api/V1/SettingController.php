<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Services\Setting\SettingService;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {
        $this->authorizeResource(Setting::class, 'setting');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return response()->json($this->settingService->all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingRequest $request)
    {
        $setting = $this->settingService->create($request);

        if (!$setting) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Data Save Successfully', 'data' => $setting], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        $settingUpdated = $this->settingService->update($request, $setting);

        if (!$settingUpdated) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Data Save Successfully', 'data' => $settingUpdated], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
