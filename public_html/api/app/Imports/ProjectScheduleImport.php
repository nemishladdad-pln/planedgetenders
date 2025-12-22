<?php

namespace App\Imports;

use App\Models\MaterialWorkType;
use App\Models\ProjectSchedule;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;


class ProjectScheduleImport implements ToModel,
                                        WithHeadingRow,
                                        WithValidation,
                                        SkipsOnError,
                                        SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $project_id;

    public function __construct($projectId)
    {
        $this->project_id = $projectId;
    }
    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    /**
     * @param \Throwable $e
     */
    /*public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }*/

    public function onFailure(Failure ...$failures)
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function model(array $row)
    {
        if ($row['task'] != '') {
            $input = [
                'project_id' => $this->project_id,
                'task'=> $row['task'],
                'start_date'=> Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date'])),
                'end_date'=> Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date'])),
            ];
            return ProjectSchedule::create($input);
        }

    }


    public function rules(): array
    {
        return [
            // 'task' => 'required',
            // '*task' => 'required',
            // 'start_date' => 'required',
            // '*start_date' => 'required',
            // 'end_date' => 'required',
            // '*end_date' => 'required',
        ];
    }
}
