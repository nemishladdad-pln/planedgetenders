<?php
namespace App\Repositories\Interfaces;

Interface ContractorTenderWorkOrderRepositoryInterface {

    public function all($request, $userId = null);

    public function create($data, $userId = null);

    public function update($data, $contractorTender);

    public function delete($data);

    public function check_work_order_already_generated($contractorTenderId);

}
