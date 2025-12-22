<?php
namespace App\Repositories;

use App\Http\Requests\StoreNoteRequest;
use App\Models\User;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

// use App\Models\Permission;

class NoteRepository implements NoteRepositoryInterface
{

    public function all($request)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        return NoteResource::collection(
            Note::when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('expires_at', 'like', '%'. request('search') . '%');
            })->where('user_id', Auth::user()->id)->orderBy($field, $order)->paginate($perPage)
        );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function create($request)
    {
        $inputs = [
            'user_id' => Auth::user()->id,
            'note' => $request->note,
            'expires_at' => $request->expires_at,
        ];
        return Note::create($inputs);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $note
     * @return Mixed
     */
    public function update($request, $note): mixed
    {
        if (!$note) {
            return false;
        }
        $note->note = $request->note;
        $note->expires_at = $request->expires_at;

        return $note->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return Response
     */
    public function delete($note)
    {
        return $note->destroy();
    }
}
