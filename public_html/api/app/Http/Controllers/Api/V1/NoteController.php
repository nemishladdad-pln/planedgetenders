<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Services\Note\NoteService;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct(protected NoteService $noteService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->noteService->all($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        if (!$request->validated()) {
            return false;
        }
        $note = $this->noteService->create($request);

        if (!$note) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Note created Successfully', 'data' => $note], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return NoteResource::make(Note::findOrFail($note->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        if (!$request->validated()) {
            return false;
        }
        $note =  $this->noteService->update($request, $note);
        if (!$note) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'Note updated Successfully', 'data' => $note], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note = $this->noteService->delete($note);

        if (!$note) {
            return response()->json(['message' => 'There are a few errors while removing note. Please check again.'], 403);
        }
        return response()->json(['message' => 'Deleted Successfully', 'data' => $note], 201);
    }
}
