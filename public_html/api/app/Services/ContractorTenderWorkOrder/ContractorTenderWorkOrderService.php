<?php

namespace App\Services\ContractorTenderWorkOrder;

use App\Repositories\Interfaces\ContractorTenderWorkOrderRepositoryInterface;

class ContractorTenderWorkOrderService
{
    public function __construct(protected ContractorTenderWorkOrderRepositoryInterface $contractorTenderWorkOrderRepository) { }

    public function all($request, $userId = null)
    {
        return $this->contractorTenderWorkOrderRepository->all($request, $userId);
    }

    public function create($data, $userId = null)
    {
        return $this->contractorTenderWorkOrderRepository->create($data, $userId);
    }

    public function update($data, $userId = null)
    {
        return $this->contractorTenderWorkOrderRepository->update($data, $userId);
    }

}
