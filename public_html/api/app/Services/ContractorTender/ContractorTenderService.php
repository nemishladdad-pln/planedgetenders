<?php

namespace App\Services\ContractorTender;

use App\Models\ContractorTender;
use App\Repositories\Interfaces\ContractorTenderRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ContractorTenderService
{
    public function __construct(protected ContractorTenderRepositoryInterface $contractorTenderRepository) { }

    public function all($request)
    {
        return $this->contractorTenderRepository->all($request);
    }

    public function tender_wise($request, $userId = null)
    {
        return $this->contractorTenderRepository->tender_wise($request, $userId);
    }

    public function create($data)
    {
        return $this->contractorTenderRepository->create($data);

    }

    public function my_tenders($request, $userId)
    {
        return $this->contractorTenderRepository->my_tenders($request, $userId);
    }

    public function no_of_tenders($request)
    {
        return $this->contractorTenderRepository->no_of_tenders($request);
    }


    public function automatic_comparison($tender)
    {
        return $this->contractorTenderRepository->automatic_comparison($tender);
    }

    public function contractors_bids($request, $userId)
    {
        return $this->contractorTenderRepository->contractors_bids($request, $userId);
    }

}
