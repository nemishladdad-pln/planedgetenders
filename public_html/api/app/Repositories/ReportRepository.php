<?php
namespace App\Repositories;

use App\Http\Resources\ContractorResource;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\Contractor;
use App\Models\ContractorCategoryRating;
use App\Models\ContractorEvaluation;
use App\Models\Organization;
use App\Models\ProfileUser;
use App\Models\Project;
use App\Models\Role;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use App\Models\Permission;

class ReportRepository implements ReportRepositoryInterface
{

    /**
     * A description of the entire PHP function.
     *
     * @param int $user description
     * @return array
     */
    public function overall_performance($user, $year = null)
    {
        if ($year == null) {
            $year = date('Y');
        }
        $response = [];
        $roles = $this->_getUserRoles($user);
        $isAdmin = false;
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());
        } else if (in_array('Site Project Manager', $roles)) {
            $projectIds = array_keys(Project::where('site_project_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('General Manager', $roles)) {
            $projectIds = array_keys(Project::where('general_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $isAdmin = true;
        }
        // Task 1: Count of contractors evaluated by quarter
        if ($isAdmin) {
            $contractorsCount = ContractorCategoryRating::where('year', $year)
                ->select('quarter_id')
                ->distinct('contractor_id')
                ->where('year', $year)
                ->groupBy('quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'quarter' => $item->quarter->name, // Assuming 'name' is a column in the quarters table
                        'contractor_count' => ContractorCategoryRating::where('quarter_id', $item->quarter_id)
                            ->distinct('contractor_id')
                            ->count('contractor_id'),
                    ];
                });

            // Task 2: Count of projects evaluated by quarter
            $projectsCount = ContractorCategoryRating::where('year', $year)
                ->select('quarter_id')
                ->distinct('project_id')
                ->groupBy('quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'quarter' => $item->quarter->name,
                        'project_count' => ContractorCategoryRating::where('quarter_id', $item->quarter_id)
                            ->distinct('project_id')
                            ->count('project_id'),
                    ];
                });

                // Task 3: Average rating evaluated by quarter
                $averageRatings = ContractorCategoryRating::where('year', $year)
                    ->select('quarter_id')
                    ->groupBy('quarter_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'quarter' => $item->quarter->name,
                            'average_rating' => ContractorCategoryRating::where('quarter_id', $item->quarter_id)
                                ->avg('rating'),
                        ];
                    });

                $averageRatingsByCategories = ContractorCategoryRating::where('year', $year)
                    ->select('category_rating_id', \DB::raw('AVG(rating) as average_rating'), 'quarter_id')
                    ->groupBy('category_rating_id', 'quarter_id')
                    //->groupBy(fn($item) => $item->quarter->name . '-' . $item->category_rating->name)
                    ->with('category_rating', 'quarter') // Assuming you have a relationship with the CategoryRating model
                    ->get()
                    ->map(function($rating) {
                        return [
                            'quarter' => $rating->quarter->name,
                            'category_name' => $rating->category_rating->name, // Assuming 'name' is a field in the category_ratings table
                            'average_rating' => round($rating->average_rating, 2)
                        ];
                    })
                    ->toArray();
        }  else if(isset($projectIds)) {
            $contractorsCount = ContractorCategoryRating::where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->select('quarter_id')
                ->distinct('contractor_id')
                ->where('year', $year)
                ->groupBy('quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'quarter' => $item->quarter->name, // Assuming 'name' is a column in the quarters table
                        'contractor_count' => ContractorCategoryRating::where('quarter_id', $item->quarter_id)
                            ->distinct('contractor_id')
                            ->count('contractor_id'),
                    ];
                });
            // Task 2: Count of projects evaluated by quarter
            $projectsCount = ContractorCategoryRating::where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->select('quarter_id')
                ->distinct('project_id')
                ->groupBy('quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'quarter' => $item->quarter->name,
                        'project_count' => ContractorCategoryRating::where('quarter_id', $item->quarter_id)
                            ->distinct('project_id')
                            ->count('project_id'),
                    ];
                });

            // Task 3: Average rating evaluated by quarter
            $averageRatings = ContractorCategoryRating::where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->select('quarter_id')
                ->groupBy('quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'quarter' => $item->quarter->name,
                        'average_rating' => ContractorCategoryRating::where('quarter_id', $item->quarter_id)
                            ->avg('rating'),
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->select('category_rating_id', \DB::raw('AVG(rating) as average_rating'), 'quarter_id')
                ->groupBy('category_rating_id', 'quarter_id')
                //->groupBy(fn($item) => $item->quarter->name . '-' . $item->category_rating->name)
                ->with('category_rating', 'quarter') // Assuming you have a relationship with the CategoryRating model
                ->get()
                ->map(function($rating) {
                    return [
                        'quarter' => $rating->quarter->name,
                        'category_name' => $rating->category_rating->name, // Assuming 'name' is a field in the category_ratings table
                        'average_rating' => round($rating->average_rating, 2)
                    ];
                })
                ->toArray();
        }

        if (isset($averageRatingsByCategories)) {
            // Initialize result array
            $groupedData = [];

            // Grouping data by quarter and category name
            foreach ($averageRatingsByCategories as $entry) {
                $quarter = $entry['quarter'];
                $category = $entry['category_name'];

                // Initialize the structure if it doesn't exist
                if (!isset($groupedData[$quarter])) {
                    $groupedData[$quarter] = [];
                }
                if (!isset($groupedData[$quarter][$category])) {
                    $groupedData[$quarter][$category] = [
                        'total_rating' => 0,
                        'count' => 0
                    ];
                }

                // Add rating and increment count
                $groupedData[$quarter][$category]['total_rating'] += $entry['average_rating'];
                $groupedData[$quarter][$category]['count'] += 1;
            }

            // Calculate average
            $finalData = [];
            $labels = [];
            $quarters = [];
            foreach ($groupedData as $quarter => $categories) {

                array_push($quarters, $quarter);
                foreach ($categories as $category => $values) {
                    $averageRating = $values['total_rating'] / $values['count'];
                    if (!in_array($category, $labels)) {
                        array_push($labels, $category);
                    }
                    $finalData[$quarter][] = $averageRating;
                }
            }

            $response = [
                'quarters' => $quarters,
                'contractors' => $contractorsCount,
                'projects' => $projectsCount,
                'average_ratings' => $averageRatings,
                'overall_contractor_performance' => [
                    'labels' => $labels,
                    'data' => $finalData
                ]
            ];
        }

        return $response;
    }

    public function activity_wise($user, $year = null)
    {
        if ($year == null) {
            $year = date('Y');
        }
        $response = [];
        $roles = $this->_getUserRoles($user);
        $isAdmin = false;
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());
        } else if (in_array('Site Project Manager', $roles)) {
            $projectIds = array_keys(Project::where('site_project_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('General Manager', $roles)) {
            $projectIds = array_keys(Project::where('general_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $isAdmin = true;
        }
        // Task 1: Count of contractors evaluated by quarter
        if ($isAdmin) {
            $contractorsCount = ContractorCategoryRating::select('material_work_type_id', 'quarter_id', DB::raw('COUNT(DISTINCT contractor_id) as contractor_count'))
                ->where('year', $year)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'contractor_count' => $item->contractor_count,
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::select('material_work_type_id', 'quarter_id',  DB::raw('AVG(rating) as average_rating'))
                ->where('year', $year)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'average_rating' => $item->average_rating,
                    ];
                });
        }  else if(isset($projectIds)) {
            $contractorsCount = ContractorCategoryRating::select('material_work_type_id', 'quarter_id', DB::raw('COUNT(DISTINCT contractor_id) as contractor_count'))
                ->where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'contractor_count' => $item->contractor_count,
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::select('material_work_type_id', 'quarter_id',  DB::raw('AVG(rating) as average_rating'))
                ->where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'average_rating' => $item->average_rating,
                    ];
                });
        }
        $contractors = [];
        if (isset($contractorsCount)) {
            foreach ($contractorsCount as $contractor) {
                //dd($contractor);
                $materialWorkType = $contractor['material_work_type'];
                $quarter = $contractor['quarter'];
                $contractor_count = $contractor['contractor_count'];
                if (!isset($contractors[$materialWorkType])) {
                    $contractors[$materialWorkType] = [];
                }
                if (!isset($contractors[$materialWorkType][$quarter])) {
                    $contractors[$materialWorkType][$quarter] = [
                        'count' => 0
                    ];
                }
                $contractors[$materialWorkType][$quarter]['count'] = $contractor_count;
            }
        }
        if (isset($averageRatingsByCategories)) {
            // Initialize result array
            $groupedData = $quarters = [];

            // Grouping data by quarter and category name
            foreach ($averageRatingsByCategories as $entry) {
                $quarter = $entry['quarter'];
                $category = $entry['material_work_type'];
                if (!in_array($quarter, $quarters)) {
                    array_push( $quarters,  $quarter);
                }
                // Initialize the structure if it doesn't exist
                if (!isset($groupedData[$category])) {
                    $groupedData[$category] = [];
                }
                if (!isset($groupedData[$quarter][$category])) {
                    $groupedData[$quarter][$category] = [
                        'total_rating' => 0,
                        'count' => 0
                    ];
                }

                // Add rating and increment count
                $groupedData[$quarter][$category]['total_rating'] += $entry['average_rating'];
                $groupedData[$quarter][$category]['count'] += 1;
            }

            // Calculate average
            $finalData = [];
            $labels = [];

            foreach ($groupedData as $quarter => $categories) {

                foreach ($categories as $category => $values) {
                    $averageRating = $values['total_rating'] / $values['count'];
                    if (!in_array($category, $labels)) {
                        array_push($labels, $category);
                    }
                    $finalData[$quarter][] = $averageRating;
                }
            }

            $response = [
                'quarters' => $quarters,
                'contractors' => $contractors,
                'performance' => [
                    'labels' => $labels,
                    'data' => $finalData
                ]
            ];
        }

        return $response;
    }

    public function grade_wise($user, $year = null)
    {
        if ($year == null) {
            $year = date('Y');
        }
        $response = [];
        $roles = $this->_getUserRoles($user);
        $isAdmin = false;
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());
        } else if (in_array('Site Project Manager', $roles)) {
            $projectIds = array_keys(Project::where('site_project_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('General Manager', $roles)) {
            $projectIds = array_keys(Project::where('general_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $isAdmin = true;
        }
        // Task 1: Count of contractors evaluated by quarter
        if ($isAdmin) {
            $contractorsCount = ContractorCategoryRating::select('material_work_type_id', 'quarter_id', DB::raw('COUNT(DISTINCT contractor_id) as contractor_count'))
                ->where('year', $year)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'contractor_count' => $item->contractor_count,
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::select('material_work_type_id', 'quarter_id',  DB::raw('AVG(rating) as average_rating'))
                ->where('year', $year)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'average_rating' => $item->average_rating,
                    ];
                });
        }  else if(isset($projectIds)) {
            $contractorsCount = ContractorCategoryRating::select('material_work_type_id', 'quarter_id', DB::raw('COUNT(DISTINCT contractor_id) as contractor_count'))
                ->where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'contractor_count' => $item->contractor_count,
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::select('material_work_type_id', 'quarter_id',  DB::raw('AVG(rating) as average_rating'))
                ->where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->with(['material_work_type', 'quarter'])
                ->groupBy('material_work_type_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'material_work_type' => $item->material_work_type->name,
                        'quarter' => $item->quarter->name,
                        'average_rating' => $item->average_rating,
                    ];
                });
        }
        $contractors = [];
        if (isset($contractorsCount)) {
            foreach ($contractorsCount as $contractor) {
                //dd($contractor);
                $materialWorkType = $contractor['material_work_type'];
                $quarter = $contractor['quarter'];
                $contractor_count = $contractor['contractor_count'];
                if (!isset($contractors[$materialWorkType])) {
                    $contractors[$materialWorkType] = [];
                }
                if (!isset($contractors[$materialWorkType][$quarter])) {
                    $contractors[$materialWorkType][$quarter] = [
                        'count' => 0
                    ];
                }
                $contractors[$materialWorkType][$quarter]['count'] = $contractor_count;
            }
        }
        if (isset($averageRatingsByCategories)) {
            // Initialize result array
            $groupedData = $quarters = [];

            // Grouping data by quarter and category name
            foreach ($averageRatingsByCategories as $entry) {
                $quarter = $entry['quarter'];
                $category = $entry['material_work_type'];
                if (!in_array($quarter, $quarters)) {
                    array_push( $quarters,  $quarter);
                }
                // Initialize the structure if it doesn't exist
                if (!isset($groupedData[$category])) {
                    $groupedData[$category] = [];
                }
                if (!isset($groupedData[$quarter][$category])) {
                    $groupedData[$quarter][$category] = [
                        'total_rating' => 0,
                        'count' => 0
                    ];
                }

                // Add rating and increment count
                $groupedData[$quarter][$category]['total_rating'] += $entry['average_rating'];
                $groupedData[$quarter][$category]['count'] += 1;
            }

            // Calculate average
            $finalData = [];
            $labels = [];

            foreach ($groupedData as $quarter => $categories) {

                foreach ($categories as $category => $values) {
                    $averageRating = $values['total_rating'] / $values['count'];
                    if (!in_array($category, $labels)) {
                        array_push($labels, $category);
                    }
                    $finalData[$quarter][] = $averageRating;
                }
            }

            $response = [
                'quarters' => $quarters,
                'contractors' => $contractors,
                'performance' => [
                    'labels' => $labels,
                    'data' => $finalData
                ]
            ];
        }

        return $response;
    }

    public function performance_evaluation($user)
    {
        "SELECT cr.name AS category_name,
                    SUM(ccr.rating) AS total_rating,
                    COUNT(ccr.rating) AS total_responses,
                    (SUM(ccr.rating) / COUNT(ccr.rating)) AS average_rating
            FROM contractor_category_ratings ccr
            JOIN category_ratings cr ON ccr.category_rating_id = cr.id
            GROUP BY cr.name
            ORDER BY cr.id;";
    }

    public function factor_performance($user)
    {

    }

    private function _getUserRoles($userId)
    {
        $user = User::findOrFail($userId);
        return $user->roles->pluck('name')->toArray();
    }




    public function gm_wise($user, $year = null)
    {
        if ($year == null) {
            $year = date('Y');
        }
        $response = [];
        $roles = $this->_getUserRoles($user);
        $isAdmin = false;
        if (in_array('Organization', $roles)) {
            $organization = Organization::where('user_id', $user)->first();
            $projectIds = array_keys($organization->projects->pluck('name', 'id')->toArray());
        } else if (in_array('Site Project Manager', $roles)) {
            $projectIds = array_keys(Project::where('site_project_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('General Manager', $roles)) {
            $projectIds = array_keys(Project::where('general_manager_id', $user)->pluck('name', 'id')->toArray());
        } else if (in_array('Super Admin', $roles) || in_array('Admin', $roles)) {
            $isAdmin = true;
        }
        // Task 1: Count of contractors evaluated by quarter
        if ($isAdmin) {
            $contractorsCount = ContractorCategoryRating::select('site_manager_id', 'quarter_id', DB::raw('COUNT(DISTINCT contractor_id) as contractor_count'))
                ->where('year', $year)
                ->with(['site_manager', 'quarter'])
                ->groupBy('site_manager_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'site_manager' => $item->site_manager->name,
                        'quarter' => $item->quarter->name,
                        'contractor_count' => $item->contractor_count,
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::select('site_manager_id', 'quarter_id',  DB::raw('AVG(rating) as average_rating'))
                ->where('year', operator: $year)
                ->with(['site_manager', 'quarter'])
                ->groupBy('site_manager_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'site_manager' => $item->site_manager->name,
                        'quarter' => $item->quarter->name,
                        'average_rating' => $item->average_rating,
                    ];
                });
        }  else if(isset($projectIds)) {
            $contractorsCount = ContractorCategoryRating::select('site_manager_id', 'quarter_id', DB::raw('COUNT(DISTINCT contractor_id) as contractor_count'))
                ->where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->with(['site_manager', 'quarter'])
                ->groupBy('site_manager_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'site_manager' => $item->site_manager->name,
                        'quarter' => $item->quarter->name,
                        'contractor_count' => $item->contractor_count,
                    ];
                });

            $averageRatingsByCategories = ContractorCategoryRating::select('site_manager_id', 'quarter_id',  DB::raw('AVG(rating) as average_rating'))
                ->where('year', $year)
                ->whereIn('project_id', $projectIds)
                ->with(['site_manager', 'quarter'])
                ->groupBy('site_manager_id', 'quarter_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'site_manager' => $item->site_manager->name,
                        'quarter' => $item->quarter->name,
                        'average_rating' => $item->average_rating,
                    ];
                });
        }
        $contractors = [];
        if (isset($contractorsCount)) {
            foreach ($contractorsCount as $contractor) {
                //dd($contractor);
                $materialWorkType = $contractor['site_manager'];
                $quarter = $contractor['quarter'];
                $contractor_count = $contractor['contractor_count'];
                if (!isset($contractors[$materialWorkType])) {
                    $contractors[$materialWorkType] = [];
                }
                if (!isset($contractors[$materialWorkType][$quarter])) {
                    $contractors[$materialWorkType][$quarter] = [
                        'count' => 0
                    ];
                }
                $contractors[$materialWorkType][$quarter]['count'] = $contractor_count;
            }
        }
        if (isset($averageRatingsByCategories)) {
            // Initialize result array
            $groupedData = $quarters = [];

            // Grouping data by quarter and category name
            foreach ($averageRatingsByCategories as $entry) {
                $quarter = $entry['quarter'];
                $category = $entry['site_manager'];
                if (!in_array($quarter, $quarters)) {
                    array_push( $quarters,  $quarter);
                }
                // Initialize the structure if it doesn't exist
                if (!isset($groupedData[$category])) {
                    $groupedData[$category] = [];
                }
                if (!isset($groupedData[$quarter][$category])) {
                    $groupedData[$quarter][$category] = [
                        'total_rating' => 0,
                        'count' => 0
                    ];
                }

                // Add rating and increment count
                $groupedData[$quarter][$category]['total_rating'] += $entry['average_rating'];
                $groupedData[$quarter][$category]['count'] += 1;
            }

            // Calculate average
            $finalData = [];
            $labels = [];

            foreach ($groupedData as $quarter => $categories) {

                foreach ($categories as $category => $values) {
                    $averageRating = $values['total_rating'] / $values['count'];
                    if (!in_array($category, $labels)) {
                        array_push($labels, $category);
                    }
                    $finalData[$quarter][] = $averageRating;
                }
            }

            $response = [
                'quarters' => $quarters,
                'contractors' => $contractors,
                'performance' => [
                    'labels' => $labels,
                    'data' => $finalData
                ]
            ];
        }

        return $response;
    }
}
