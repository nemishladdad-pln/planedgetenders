<?php
namespace App\Repositories\Interfaces;

Interface SiteManagerRepositoryInterface {

    public function all($request, $userId);

    public function evaluate($data, $userId);

    public function findByContractorTender($id);

    public function checkIfContractorIsAlreadyEvaluatedForQuarter($contractorId);

    public function showEvaluatedContractorDetails($contractorId);
}
