<?php
namespace App\Repositories;

use App\Http\Requests\StoreProfileUserRequest;
use App\Http\Requests\UpdateProfileUserRequest;
// use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Repositories\Interfaces\ProfileUserRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProfileUserResource;
// use App\Models\Permission;

class ProfileUserRepository implements ProfileUserRepositoryInterface
{

    public function all($request)
    {
        //
    }

    public function create($data)
    {
        //
    }

    public function update($data, $user)
    {
        $userToUpdate = User::findOrFail($user->id);
        

        if ($data->isChangePassword) {
            $inputs['password'] = Hash::make($data->password);
        } else {
            $inputs = [
                'mobile'=> $data->mobile,
                'google2fa_enabled' => $data->google2fa_enabled,
                
            ];
            if (!empty($data->file('avatar'))) {
                $input['avatar'] = save_image($data->file('avatar'), 'users', $user->id);
            }
            
        }
        $userToUpdate->update($inputs);

        return $userToUpdate;
    }
}
