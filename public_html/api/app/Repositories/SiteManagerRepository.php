<?php
namespace App\Repositories;

use App\Http\Resources\ContractorTenderResource;
use App\Models\CategoryRating;
use App\Models\Contractor;
use App\Models\ContractorCategoryRating;
use App\Models\ContractorEvaluation;
use App\Models\ContractorTender;
use App\Models\Quarter;
use App\Models\User;
use App\Repositories\Interfaces\SiteManagerRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class SiteManagerRepository implements SiteManagerRepositoryInterface
{
    public function all($request, $userId)
    {
        $field = $request->input('sort_field') ?? 'id';
        $order = $request->input('sort_order') ?? 'desc';
        $perPage = $request->input('per_page') ?? 10;

        return ContractorTenderResource::collection(
            ContractorTender::when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('cuid', 'like', '%'. request('search') . '%');
            })->where('is_awarded', 1)->orderBy($field, $order)->paginate($perPage)
        );
    }

    public function findByContractorTender($contractorTenderId)
    {
        //First we need to get necessary information.
        $contractorTender = ContractorTenderResource::make(ContractorTender::findOrFail($contractorTenderId));
        return $contractorTender;
    }

    public function evaluate($data, $userId)
    {
        list($quarter, $contractorCategoryRating) = $this->checkIfContractorIsAlreadyEvaluatedForQuarter($data->contractor_id);
        if (!$contractorCategoryRating) {

            $contractorId = $data->contractor_id;
            $contractorInfo = Contractor::findOrFail($contractorId);

            $contractorTenderInfo = ContractorTender::findOrFail($data->id);
            $siteManagerId = $userId;
            $quarterId = $quarter->id;
            $totalRating = 0;
            $questionCount = 0;
            foreach ($data->rating as $rating) {
                if ($rating && $rating != 0) {
                    $categoryRating = explode('-', $rating);
                    $category = $categoryRating[0];
                    $managerRating = $categoryRating[1];

                    $categoryRatingInfo = CategoryRating::findOrFail($category);
                    $input = [
                        'contractor_id' => $contractorId,
                        'material_work_type_id' => $contractorInfo->material_work_type_id,
                        'site_manager_id' => $siteManagerId,
                        'category_rating_id' => $category,
                        'category_rating_parent_id' => $categoryRatingInfo->parent_id,
                        'quarter_id' => $quarterId,
                        'year' => date('Y'),
                        'rating' => $managerRating,
                        'contractor_tender_id' => $data->id,
                        'project_id' => $contractorTenderInfo->tender->project_id,
                    ];
                    ContractorCategoryRating::create($input);
                    $totalRating += $managerRating;
                    $questionCount++;
                }
            }
            $inputContractorEvaluation = [
                'site_manager_id' => $siteManagerId,
                'contractor_id' => $contractorId,
                'material_work_type_id' => $contractorInfo->material_work_type_id,
                'contractor_tender_id' => $data->id,
                'project_id' => $contractorTenderInfo->tender->project_id,
                'evaluation_data' => json_encode($data->rating),
                'rating_calculated' => $totalRating / $questionCount*10,
                'quarter_id' => $quarterId,
            ];
            ContractorEvaluation::create($inputContractorEvaluation);
        }
        return $data;
    }


    public function checkIfContractorIsAlreadyEvaluatedForQuarter($contractorId)
    {
        //Get the current quarter
        $quarter = Quarter::where('start_month', '<=', date('m'))->where('end_month', '>=', date('m'))->first();
        //Now we must save all the evaluated data.
        $contractorCategoryRating = ContractorCategoryRating::where('contractor_id', $contractorId)
                                                            ->where('quarter_id', $quarter->id)
                                                            ->where('year', date('Y'))
                                                            ->first();
        return [$quarter, $contractorCategoryRating];

    }


    public function showEvaluatedContractorDetails($contractorTenderId)
    {
        $response = [];
        $contractorTenderInfo = $this->findByContractorTender($contractorTenderId);

        $categoryRatings = ContractorCategoryRating::where('contractor_id', $contractorTenderInfo->contractor_id)
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
            'contractor_info' => $contractorTenderInfo,
            'evaluation_report' => [
                'labels' => $labels,
                'data' => $data,
                'for_year' => date('Y'),
            ]

        ];
        return $response;
    }
}
