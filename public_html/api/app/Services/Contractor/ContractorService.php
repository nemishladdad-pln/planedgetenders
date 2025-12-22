<?php
namespace App\Services\Contractor;

use App\Http\Requests\StoreContractorRequest;
use App\Http\Requests\UpdateContractorRequest;
use App\Models\Contractor;
use App\Repositories\Interfaces\ContractorRepositoryInterface;
use Illuminate\Http\Request;

class ContractorService
{

    public function __construct(protected ContractorRepositoryInterface $contractorRepository) { }

    public function all($request, $userId = null)
    {
        return $this->contractorRepository->all($request, $userId);
    }


    public function create(StoreContractorRequest $request): mixed
    {


        return $this->contractorRepository->create($request);
    }

    public function update(Request $request, $contractor)
    {
        // if (!$request->validated()) {
        //     return false;
        // }

        return $this->contractorRepository->update($request, $contractor);
    }


    public function delete(Contractor $contractor)
    {
        return $this->contractorRepository->delete($contractor);
    }

    public function findByUser($userId) {
        return $this->contractorRepository->findByUser($userId);
    }
}
