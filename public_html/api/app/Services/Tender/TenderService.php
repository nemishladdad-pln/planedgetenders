<?php
namespace App\Services\Tender;

use App\Http\Requests\StoreTenderRequest;
use App\Models\Tender;
use App\Repositories\Interfaces\TenderRepositoryInterface;
use Illuminate\Http\Request;

class TenderService
{

    public function __construct(protected TenderRepositoryInterface $tenderRepository) { }

    public function all(Request $request, $userId =null, $status = null)
    {
        return $this->tenderRepository->all($request, $userId, $status);
    }


    public function create($request, $user = null): mixed
    {
        return $this->tenderRepository->create($request, $user);
    }



    public function show(Tender $tender): mixed
    {
        if (!$tender) {
            return false;
        }
        return $this->tenderRepository->show($tender);
    }

    public function update($data, $tender)
    {
        return $this->tenderRepository->update($data, $tender);
    }

    public function upcoming_tenders($request, $userId =null)
    {
        return $this->tenderRepository->upcoming_tenders($request, $userId);
    }
}
