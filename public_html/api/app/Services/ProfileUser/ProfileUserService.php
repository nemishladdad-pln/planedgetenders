<?php
namespace App\Services\ProfileUser;

use App\Http\Requests\StoreProfileUserRequest;
use App\Http\Requests\UpdateProfileUserRequest;
use App\Repositories\Interfaces\ProfileUserRepositoryInterface;
use App\Models\ProfileUser;
use Illuminate\Http\Request;

class ProfileUserService
{

    public function __construct(protected ProfileUserRepositoryInterface $profileuserRepository) { }

    public function all($request)
    {
        return $this->profileuserRepository->all($request);
    }


    public function create(StoreProfileUserRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->profileuserRepository->create($request);
    }


    public function update(Request $request, $profileUser)
    {
        /*if (!$request->validated()) {
            return false;
        }*/

        return $this->profileuserRepository->update($request, $profileUser);
    }


}

