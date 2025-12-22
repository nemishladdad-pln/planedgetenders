<?php

namespace App\Exports;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $userId = Auth::id();
        return PaymentResource::collection(Payment::where('user_id', $userId)->get());
       
    }

} 