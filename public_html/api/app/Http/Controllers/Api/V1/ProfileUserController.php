<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProfileUser;
use App\Http\Requests\StoreProfileUserRequest;
use App\Http\Requests\UpdateProfileUserRequest;
use App\Services\ProfileUser\ProfileUserService;
use App\Services\Contractor\ContractorService;
use App\Http\Resources\ProfileUserResource;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Interfaces\ContractorRepositoryInterface;
use App\Services\Organization\OrganizationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class ProfileUserController extends Controller
{
    public function __construct(
        protected ProfileUserService $profileUserService,
        protected ContractorService $contractorService,
        protected OrganizationService $organizationService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $response = ['user' => $user, 'role' => implode(', ', Auth::user()->roles->pluck('name')->toArray())];
        $userRole = '';
        if ($response['role']) {
            $userRole = $response['role'];
        }
        if ($userRole === 'Contractor') {
            $response = $this->contractorService->findByUser(Auth::user()->id);
        } else if ($userRole === 'Organization') {
            $response = $this->organizationService->findByUser(Auth::user()->id);
        }
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     */
    public function store(StoreProfileUserRequest $request)
    {
       $user = $this->profileUserService->update($request, $user);
        if (!$user) {
            return response()->json(['message' => 'There are a few errors in form. Please check again.'], 403);
        }
        return response()->json(['message' => 'ProfileUser created Successfully', 'data' => $user], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateProfileUserRequest $request, ProfileUser $profileUser)
    // {
        // $inputs = $response = [];
        // // First of all we need to check which form is submitted.
        // // Change Password for Account information form.
        // $user = Auth::user();

        // if ($request->isChangePassword) {
        //     $eloquent = User::findOrFail($user->id);
        //     $inputs = [
        //         'password' => Hash::make($request->password),
        //     ];
        // } else {
        //     // Else: User profile information will be updated.
        //     $inputs = [
        //         'mobile_no' => $request->mobile_no
        //     ];
        //     // Check if image was given and save on local file system

        //   }
    // }

    public function update(Request $request, User $user)
    {
        $user = $this->profileUserService->update($request, auth()->user());

        if (!$user) {
            return response()->json(['message' => 'There are a few errors in the form. Please check again.'], 403);
        }

        return response()->json(['message' => 'ProfileUser updated Successfully', 'data' => $user], 200);
    }

    // public function changePassword(Request $request)
    // {
    //     $request->validate([
    //         'old_password' => 'required',
    //         'new_password' => 'required|confirmed',
    //     ]);

    //     $user = Auth::user();

    //     // Verify the old password
    //     if (!Hash::check($request->old_password, $user->password)) {
    //         return redirect()->back()->with('error', 'The provided old password is incorrect.');
    //     }

    //     // Update the user's password
    //     $user->update([
    //         'password' => Hash::make($request->new_password),
    //     ]);

    //     return response()->json(['success', 'Password changed successfully!']);
    // }
}
