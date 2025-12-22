<?php
namespace App\Services\Report;

use App\Repositories\Interfaces\ReportRepositoryInterface;

class ReportService
{

    public function __construct(protected ReportRepositoryInterface $reportRepository) { }

    public function overall_performance($user)
    {
        return $this->reportRepository->overall_performance($user);
    }

    public function activity_wise($user)
    {
        return $this->reportRepository->activity_wise($user);
    }

    public function grade_wise($user)
    {
        return $this->reportRepository->grade_wise($user);
    }

    public function performance_evaluation($user)
    {
        return $this->reportRepository->performance_evaluation($user);
    }

    public function factor_performance($user)
    {
        return $this->reportRepository->factor_performance($user);
    }

    public function gm_wise($user)
    {
        return $this->reportRepository->gm_wise($user);
    }
}
