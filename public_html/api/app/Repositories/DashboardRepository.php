<?php
namespace App\Repositories;

use App\Http\Resources\ContractorCategoryRatingResource;
use App\Models\User;
use App\Models\Tender;
use App\Models\Contractor;
use App\Models\ContractorWork;
use App\Models\ContractorTender;
use App\Models\ContractorContact;
use App\Models\ContractorDocument;
use App\Models\ContractorTurnover;
use App\Models\ContractorEquipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ContractorResource;
use App\Http\Resources\ContractorTenderResource;
use App\Http\Resources\TenderResource;
use App\Http\Resources\UserResource;
use App\Models\ContractorCategoryRating;
use App\Models\ContractorQualityCertificate;
use App\Models\ContractorDirectorTechnicalStaff;
use App\Models\ContractorEvaluation;
use App\Models\ContractorTenderWorkOrder;
use App\Models\MaterialWorkType;
use App\Models\Note;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Quarter;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function all($userId = null)
    {

        $user = User::findOrFail($userId);

        $response = [
            'basic_info' => $this->_getBasicInfo($user),
            'general_report' => $this->_getGeneralReport($user),
            'top_tenders' => $this->_getTopTenders($user),
            'contractors_in_month' => $this->_getContractorsInMonth($user),
            'activity_wise_contract_appointment' => $this->_getActivityWiseContractAppointment($user),
            'contractor_appointment_under_budget' => $this->_getContractorAppointmentUnderBudget($user),
            'recent_activities' => $this->_getRecentActivities($user),
            'current_active_tenders' => $this->_getCurrentActiveTenders($user),
            'important_notes' => $this->_getImportantNotes($user),
            'schedules' => $this->_getSchedules($user),
            'tender_updates' => $this->_getTenderUpdates($user),
            'top_three_bids' => $this->_getTopThreeBids($user),
            'chart_sales_reports' => $this->_getChartSalesReports($user),
            'evaluation_report' => $this->_getEvaluationReport($user),
        ];
        $roles = $user->roles->pluck('name')->toArray();
        $response['user_role'] = implode(',', $roles);
        return $response;
    }

    private function _getBasicInfo($user)
    {
        $response = [];
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {
            $response = Organization::where('user_id', $user->id)->first();
        } else if (in_array('Contractor', $roles)) {
            $response = ContractorResource::make(Contractor::where('user_id', $user->id)->first());
        } else if (in_array('Site Project Manager', $roles)) {
            $response = $this->_getManagerGeneralReport($user);
        }
        return $response;
    }

    private function _getManagerGeneralReport($user = null)
    {
        $response = [];
        if ($user) {
            // Get total number of awarded tenders for this project
            $project = Project::where('site_project_manager_id', $user->id)->first();
            $response['awarded_tender_count'] = Tender::where('project_id', $project->id)->where('awarded_to', '!=', null)->count();

            // Get total number of contractors evaluated.
            $response['contractor_evaluated_count'] = ContractorEvaluation::where('site_manager_id', $user->id)
                                    ->groupBy('contractor_id')
                                    ->count();
        }
        return $response;
    }
    private function _getGeneralReport($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();

        $response = [];
        if (in_array('Organization', $roles)) {
            //Now we must get list of all the project of this organization.
            $organization = Organization::where('user_id', $user->id)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());

            $response['active_tender_count'] = Tender::where('status', 'active')
                            ->where('project_id', $projectIds)
                            ->count();

            $response['number_of_projects'] = count($projectIds);

            $pendingAwards = ContractorTenderWorkOrder::where('organization_id', $organization->id)
                            ->where('is_organization_approved', 0)
                            ->where('is_awarded', 0)
                            ->count();

            $response['number_of_pending_bids_to_award'] = $pendingAwards;

            $tenders = Tender::where('status', 'active')
                            ->where('project_id', $projectIds)
                            ->get();

            $tenderIds = $tenders->pluck('id', 'project_id')->toArray();

            $discussionCount = ContractorTender::where('tender_id', $tenderIds)
                                    ->where('is_awarded', 0)
                                    ->count();
            $response['number_of_bids_under_discussion'] = $discussionCount;

        } else if (in_array('Contractor', $roles)) {
            $contractorInfo = Contractor::where('user_id', $user->id)->first();
            $response['active_tender_count'] = Tender::where('status', 'active')
                                                    ->where('material_work_type_id', $contractorInfo->material_work_type_id)
                                                    ->count();
            $discussionCount = ContractorTender::where('contractor_id', $contractorInfo->id)
                                    ->where('is_awarded', 0)
                                    ->where('status', 'active')
                                    ->count();

            $response['number_of_bids_under_discussion'] = $discussionCount;

            $pendingAwards = ContractorTenderWorkOrder::where('contractor_id', $contractorInfo->id)
                            ->where('is_organization_approved', 0)
                            ->where('is_contractor_approved', 0)
                            ->where('is_awarded', 0)
                            ->count();

            $response['number_of_pending_bids_to_award'] = $pendingAwards;

            $response['rating'] = (ContractorEvaluation::where('contractor_id', $contractorInfo->id)
                                                        ->avg('rating_calculated')) / 10;

        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $response['active_tender_count'] = Tender::where('status', 'active')->count();
            $pendingAwards = ContractorTenderWorkOrder::where('is_organization_approved', 0)
                            ->where('is_awarded', 0)
                            ->count();
            $response['number_of_pending_bids_to_award'] = $pendingAwards;

            $discussionCount = ContractorTender::where('is_awarded', 0)
                                    ->count();
            $response['number_of_bids_under_discussion'] = $discussionCount;
        }

        // Get count of active tenders

        $response['new_registrations'] = Contractor::where('created_at', '>=', now()->subMonth())->count();
        $response['bid_submission_start_date'] = Tender::where('status', 'active')->count();
        $response['is_awarded'] = ContractorTender::where('is_awarded', 'true')->count();

        return $response;
    }
    private function _getTopTenders($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user->id)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());
            return Tender::where('status', 'active')->where('project_id', $projectIds)->latest()->take(3)->get();
        } else if (in_array('Contractor', $roles)) {

        }
        return Tender::where('status', 'active')->latest()->take(3)->get();
    }
    private function _getContractorsInMonth($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {

        } else if (in_array('Contractor', $roles)) {

        }
        $response = [];
        return $response;
    }
    private function _getActivityWiseContractAppointment($user = null)
    {
        $response = [];
        $roles = $user->roles->pluck('name')->toArray();
        $labels = $data = [];
        if (in_array('Super Admin', $roles)) {
            $contractors = DB::table('contractors')
                            ->select('material_work_type_id', DB::raw('count(*) as total'))
                            ->groupBy('material_work_type_id')
                            ->get();
        } else if (in_array('Organization', $roles)) {
            $contractors = DB::table('contractors')
                            ->select('material_work_type_id', DB::raw('count(*) as total'))
                            ->groupBy('material_work_type_id')
                            ->get();
        }

        if (isset($contractors) && !empty($contractors)) {
            foreach ($contractors as $contractor) {
                $materialWork = MaterialWorkType::findOrFail($contractor->material_work_type_id);
                array_push($labels, $materialWork->name);
                array_push($data, $contractor->total);
            }
        }

        $response['labels'] = $labels;
        $response['data'] = $data;
        return $response;
    }
    private function _getContractorAppointmentUnderBudget($user = null)
    {
        $response = [];
        $roles = $user->roles->pluck('name')->toArray();

        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user->id)->first();

            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());

            $totalAwardedCount = Tender::where('awarded_on', '!=', null)->whereIn('project_id', $projectIds)->count();
            $underBudget = Tender::where('awarded_on', '!=', null)->where('is_under_budget', 1)->whereIn('project_id', $projectIds)->count();

        } else if (in_array('General Manager', $roles)) {
            $projects = Project::where('general_manager_id', $user->id)->get()->pluck('name', 'id')->toArray();

            $projectIds = array_keys($projects);
            $totalAwardedCount = Tender::where('awarded_on', '!=', null)->whereIn('project_id', $projectIds)->count();
            $underBudget = Tender::where('awarded_on', '!=', null)->where('is_under_budget', 1)->whereIn('project_id', $projectIds)->count();

        } else if (in_array('Super Admin', $roles)) {
            $totalAwardedCount = Tender::where('awarded_on', '!=', null)->count();
            $underBudget = $response = Tender::where('awarded_on', '!=', null)->where('is_under_budget', 1)->count();
        }

        if (isset($underBudget)) {
            $response = [
                'labels' => ['Under Budget', 'Out of Budget'],
                'data' => [$underBudget, ($totalAwardedCount - $underBudget)]
            ];
        }


        return $response;
    }
    private function _getRecentActivities($user = null)
    {
        $response= [];
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {

        } else if (in_array('Contractor', $roles)) {
            $contractor = Contractor::where('user_id', $user->id)->first();
            $tenders = Tender::where('material_work_type_id', $contractor->material_work_type_id)->get();
            $tenderReferences = array_keys($tenders->pluck('tender_reference_number', 'id')->toArray());
            $response = Activity::where('log_name', 'tender')
                                ->orWhere('log_name', 'bid')
                                ->whereIn('properties->tender', $tenderReferences)
                                ->where('created_at', '>=', Carbon::now()->subWeek())
                                ->latest()
                                // ->take(3)
                                ->get();
        } else if (in_array('Super Admin', $roles)) {
            $response = Activity::where('log_name', 'tender')
                                ->orWhere('log_name', 'bid')
                                ->where('created_at', '>=', Carbon::now()->subWeek())
                                ->latest()
                                // ->take(3)
                                ->get();
        } else {
            $response = Activity::where('log_name', 'tender')
                                ->orWhere('log_name', 'bid')
                                ->where('created_at', '>=', Carbon::now()->subWeek())
                                ->latest()
                                // ->take(3)
                                ->get();
        }
        //
        //$response = [];
        return $response;
    }
    private function _getCurrentActiveTenders($user = null)
    {
        $response = [];
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user->id)->first();

            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());

            $response = TenderResource::collection(Tender::whereHas('contractor_tenders', function($query) {
                $query->where('status', 'active');
            })->where('project_id', $projectIds)->where('status', 'active')->latest()->get());

        } else if (in_array('Contractor', $roles)) {
            $contractorInfo = Contractor::where('user_id', $user->id)->first();

            $response = TenderResource::collection(Tender::where('material_work_type_id', $contractorInfo->material_work_type_id)
                                                        ->where('status', 'active')->latest()->get());
        } else if (in_array('Super Admin', $roles)) {
            $response = TenderResource::collection(
                Tender::whereHas('contractor_tenders', function($query) {
                    $query->where('status', 'active');
                })->where('status', 'active')->latest()->get());

        }
        return $response;
    }
    private function _getImportantNotes($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {

        } else if (in_array('Contractor', $roles)) {

        }
        return Note::where('user_id', $user->id)->latest()->take(3)->get();
    }
    private function _getSchedules($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {

        } else if (in_array('Contractor', $roles)) {

        }
        $response = [];
        return $response;
    }
    private function _getTenderUpdates($user = null)
    {
        $response = [];
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user->id)->first();

            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());

            $response = TenderResource::collection(Tender::whereHas('contractor_tenders',
                function($query) {
                    $query->where('status', 'active')
                        ->where('bid_submission_end_date', '>=', Carbon::parse(now())
                    );
            })->where('project_id', $projectIds)->paginate());
        } else if (in_array('Contractor', $roles)) {
            $contractor = Contractor::where('user_id', $user->id)->first();
            $response = TenderResource::collection(Tender::where('status', 'active')
                                                            ->where('bid_submission_end_date', '>=', Carbon::parse(now()))
                                                            ->where('material_work_type_id', $contractor->material_work_type_id)
                                                            ->paginate());
        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $response = TenderResource::collection(Tender::whereHas('contractor_tenders',
                function($query) {
                    $query->where('status', 'active')
                        ->where('bid_submission_end_date', '>=', Carbon::parse(now())
                        ->format('Y-m-d')
                        );
            })->paginate());
        } else if (in_array('Site Project Manager', $roles)) {
            $projects = Project::where('site_project_manager_id', $user->id)->get();
            $projectIds = array_keys($projects->pluck('name', 'id')->toArray());

            $response = TenderResource::collection(Tender::where('status', 'active')
                                                        ->where('bid_submission_end_date', '>=', Carbon::parse(now())->format('Y-m-d'))
                                                        ->where('project_id', $projectIds)
                                                        ->paginate()
                                                    );
        }
        return $response;
    }
    private function _getTopThreeBids($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();
        $response = [];
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user->id)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());
            $response = ContractorTenderResource::collection(ContractorTender::whereHas('tender', function($query) use ($projectIds){
                $query->where('project_id', $projectIds)
                    ->where('created_at', '>=', Carbon::parse(now()->subMonth())->format('Y-m-d'));
            })->where('is_awarded', 0)->latest()->get());
        } else if (in_array('Site Project Manager', $roles)) {
            $projects = Project::where('site_project_manager_id', $user->id)->get();
            $projectIds = array_keys($projects->pluck('name', 'id')->toArray());
            $response = ContractorTenderResource::collection(ContractorTender::whereHas('tender', function($query) use ($projectIds){
                $query->where('project_id', $projectIds)
                ->where('created_at', '>=', Carbon::parse(now()->subMonth())->format('Y-m-d'));
            })->where('is_awarded', 0)->latest()->get());
        } else if (in_array('General Manager', $roles)) {
            $projects = Project::where('general_manager_id', $user->id)->get();
            $projectIds = array_keys($projects->pluck('name', 'id')->toArray());
            $response = ContractorTenderResource::collection(ContractorTender::whereHas('tender', function($query) use ($projectIds){
                $query->where('project_id', $projectIds)
                ->where('created_at', '>=', Carbon::parse(now()->subMonth())->format('Y-m-d'));
            })->where('is_awarded', 0)->latest()->get());
        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $response = ContractorTenderResource::collection(ContractorTender::where('is_awarded', 0)
            ->where('created_at', '>=', Carbon::parse(now()->subMonth())->format('Y-m-d'))
            ->latest()->get());
        }
        //$response['active_tenders'] = Tender::where('status', 'active')->latest()->take(3)->get();

        return $response;
    }

    private function _getChartSalesReports($user = null)
    {
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Organization', $roles)) {

        } else if (in_array('Contractor', $roles)) {

        }

        // Contractors registered this month
        $data = Contractor::select('id', 'created_at')
                        ->where('created_at', '>=', Carbon::parse(now()->subMonth())->format('Y-m-d'))
                        ->get()
                        ->groupBy(function ($data) {
                            return Carbon::parse($data->created_at)->format('d');
                        });
        $dates = [];
        $userCount = [];
        foreach ($data as $date => $values) {
            $dates[] = $date;
            $userCount[$date] = count($values);
        }
        $response['contractor_registered'] = [
            'labels' => $dates,
            'data' => $userCount,
        ];

        // Now get activity wise contractor appointment

        return $response;
    }

    private function _getPerformanceChart($user)
    {

    }

    private function _getEvaluationReport($user)
    {
        $response = [];

        $roles = $this->_getUserRoles($user);
        if (in_array('Contractor', $roles)) {
            $contractor = Contractor::where('user_id', $user->id)->first();

            if (!$contractor) {
                return $response;
            }

            $categoryRatings = ContractorCategoryRating::where('contractor_id', $contractor->id)
                                                    ->where('year', date('Y'))
                                                    ->get();


            if (!$categoryRatings->toArray()) {
                return $response;
            }

            $labels = $data = $quarters = $ratingArray = [];
            foreach ($categoryRatings as $categoryRating) {
                if (!in_array($categoryRating->quarter->name, $quarters)) {
                    $quarters[$categoryRating->quarter->name] = [];
                }
                array_push($quarters[$categoryRating->quarter->name], $categoryRating->rating);
                $ratingArray[$categoryRating->quarter->name][$categoryRating->category_rating->name][] = $categoryRating->rating;
            }
            foreach ($ratingArray as $quarter => $ratings) {
                foreach ($ratings as $name => $rating) {
                    if (!in_array($name, $labels)) {
                        array_push($labels, $name);
                    }
                    $rating = array_filter($rating);
                    if(count($rating)) {
                        $data[$quarter][] = array_sum($rating)/count($rating);
                    }
                }
            }
            $response = [
                'labels' => $labels,
                'data' => $data,
                'for_year' => date('Y'),
            ];
        }

        return $response;
    }

    private function _getUserRoles($user)
    {
        return $user->roles->pluck('name')->toArray();
    }

}
