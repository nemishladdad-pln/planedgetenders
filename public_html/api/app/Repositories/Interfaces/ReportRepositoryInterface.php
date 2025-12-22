<?php
namespace App\Repositories\Interfaces;

Interface ReportRepositoryInterface {

    public function activity_wise($user);

    public function grade_wise($user);

    public function performance_evaluation($user);

    public function factor_performance($user);

    public function overall_performance($user);

    public function gm_wise($user);
}
