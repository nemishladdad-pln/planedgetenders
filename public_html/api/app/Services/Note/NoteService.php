<?php
namespace App\Services\Note;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use Illuminate\Http\Request;

class NoteService
{

    public function __construct(protected NoteRepositoryInterface $noteRepository) { }

    public function all($request)
    {
        return $this->noteRepository->all($request);
    }

    public function create($request): mixed
    {
        return $this->noteRepository->create($request);
    }

    public function update($request, $note): mixed
    {
        return $this->noteRepository->update($request, $note);
    }

    public function delete(Note $note)
    {
        return $this->noteRepository->delete($note);
    }
}
