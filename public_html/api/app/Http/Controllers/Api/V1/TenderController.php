<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Http\Requests\StoreTenderRequest;
use App\Http\Requests\UpdateTenderRequest;
use App\Services\Tender\TenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenderController extends Controller
{
    public function __construct(protected TenderService $tenderService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->tenderService->all($request, Auth::user()->id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTenderRequest $request)
    {
        if (!$request->validated()) {
            return false;
        }

        $tender = $this->tenderService->create($request, Auth::user());

        if (!$tender) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Tender created successfully.', 'data' => $tender], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tender $tender)
    {
        return $this->tenderService->show($tender);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tender $tender)
    {
        $tender = $this->tenderService->update($request, $tender);

        if (!$tender) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Tender updated successfully.', 'data' => $tender], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tender $tender)
    {
        $response = [
            'success' => false,
            'message' => null,
            'errors' => null,
        ];
        if ($tender->delete()) {
            $response = [
                'success' => true,
                'message' => 'Tender deleted successfully.',
            ];
        }
        return response()->json($response);
    }

    public function active_tenders(Request $request)
    {
        $userId = null;
        if (Auth::check() && Auth::user()->id) {
            $userId = Auth::user()->id;
        }
        return $this->tenderService->all($request, $userId, 'active');
    }

    /**
     * Display a listing of the resource.
     */
    public function inactive_tenders(Request $request)
    {
        $userId = null;
        if (Auth::check() && Auth::user()->id) {
            $userId = Auth::user()->id;
        }
        return $this->tenderService->all($request, $userId, 'inactive');
    }
    /**
     * Display a listing of the resource.
     */
    public function upcoming_tenders(Request $request)
    {
        $userId = null;
        if (Auth::check() && Auth::user()->id) {
            $userId = Auth::user()->id;
        }
        return $this->tenderService->upcoming_tenders($request, $userId);
    }
    /**
     * Display a listing of the resource.
     */
    public function re_tenders(Request $request)
    {
        $userId = null;
        if (Auth::check() && Auth::user()->id) {
            $userId = Auth::user()->id;
        }
        return $this->tenderService->all($request, $userId, 're_tender');
    }
    /**
     * Display a listing of the resource.
     */
    public function cancelled_tenders(Request $request)
    {
        $userId = null;
        if (Auth::check() && Auth::user()->id) {
            $userId = Auth::user()->id;
        }
        return $this->tenderService->all($request, $userId, 'cancelled');
    }
}
