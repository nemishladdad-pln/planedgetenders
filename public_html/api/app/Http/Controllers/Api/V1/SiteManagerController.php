<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SiteManager\SiteManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteManagerController extends Controller
{
    public function __construct(protected SiteManagerService $siteManagerService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->siteManagerService->all($request, Auth::user()->id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->siteManagerService->evaluate($request, Auth::user()->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->siteManagerService->findByContractorTender($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
