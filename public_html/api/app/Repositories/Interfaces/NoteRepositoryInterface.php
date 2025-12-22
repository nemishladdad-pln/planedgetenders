<?php
namespace App\Repositories\Interfaces;

Interface NoteRepositoryInterface {

    public function all($request);

    public function create($data);

    public function update($data, $note);

    public function delete($note);
}
