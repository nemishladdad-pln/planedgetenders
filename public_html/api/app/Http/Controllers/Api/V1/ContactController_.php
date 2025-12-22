<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Services\Contact\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    public function __construct(protected ContactService $contactService) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->contactService->all($request);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreContactRequest $request)
    {
        dd($request);
        // Create a new record in the database
        $contact = $this->contactService->create($request);

        if (!$contact) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Send Message Successfully', 'data' => $contact], 201);
    }


}
