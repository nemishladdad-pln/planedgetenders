<?php
namespace App\Repositories\Interfaces;

Interface ContractorTenderRepositoryInterface {

    public function all($request);

    public function create($data);

    public function update($data, $contractorTender);

    public function delete($data);

    public function tender_wise($request, $userId = null);

    public function get_contractor_wise_detail($id);

    public function my_tenders($request, $userId);

    public function automatic_comparison($tender);

    public function check_tender_password($request);

    public function check_contractor_bid($id, $userId);

    public function get_tender_info($id);
}
