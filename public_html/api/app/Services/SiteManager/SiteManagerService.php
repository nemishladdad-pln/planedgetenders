<?php
namespace App\Services\SiteManager;

use App\Models\Contractor;
use App\Repositories\Interfaces\SiteManagerRepositoryInterface;
use Illuminate\Http\Request;

class SiteManagerService
{

    public function __construct(protected SiteManagerRepositoryInterface $siteManagerRepository) { }

    public function all($request, $userId)
    {
        return $this->siteManagerRepository->all($request, $userId);
    }

    public function findByContractorTender($id)
    {
        return $this->siteManagerRepository->findByContractorTender($id);
    }


    public function findByUser($userId) {
        return $this->siteManagerRepository->findByUser($userId);
    }

    public function evaluate($request, $userId)
    {
        return $this->siteManagerRepository->evaluate($request, $userId);
    }
}
