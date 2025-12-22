<?php
namespace App\Services\Contact;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Http\Request;

class ContactService
{

    public function __construct(protected ContactRepositoryInterface $contactRepository) { }

    public function all(Request $request)
    {
        return $this->contactRepository->all($request);
    }


    public function create(StoreContactRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->contactRepository->create($request);
    }
}
