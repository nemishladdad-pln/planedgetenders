<?php
namespace App\Repositories;

use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ContactResource;

class ContactRepository implements ContactRepositoryInterface
{

    public function all($request)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        // return UserResource::collection(
        //     User::when(request('search'), function ($query) {
        //         $query->where('name', 'like', '%' . request('search') . '%');
        //         $query->orWhere('email', 'like', '%' . request('search') . '%');
        //         //$query->orWhere('mobile_no', 'like', '%' . request('search') . '%');
        //     })->orderBy($field, $order)->paginate($perPage)
        // );
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
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request-> subject,
            'message' => $request-> message,

        ];
        return Contact::create($inputs);
    }

}
