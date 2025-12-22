<?php

namespace App\Imports;

use App\Models\TenderMaterialWork;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class TenderWorkListImport implements ToModel,
                                    WithHeadingRow,
                                    WithValidation,
                                    SkipsOnError,
                                    SkipsOnFailure,
                                    WithEvents
{
    use Importable, SkipsErrors, SkipsFailures;

    private $data;

    private $tender_id;

    private $material_type_id;

    public function __construct($data, $tenderId)
    {
        $this->data = $data;
        $this->tender_id = $tenderId;
    }
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param Collection $collection
    */
    public function sheets(): array
    {
        return [
            new TenderWorkListImport($this->data, $this->tender_id)
        ];
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
        if ($row['rate'] < 0 || $row['quantity'] < 0) {
            return ;
        }
        $unit = Unit::where('code', 'like', '%' . $row['unit'] . '%')->first();

        $input = [
            'tender_id' => $this->tender_id,
            'work'=> $row['work'],
            'material_work_type_id'=> $this->material_type_id,
            'unit_id'=> $unit ? $unit->id: 1,
            'rate' => $row['rate'],
            'quantity'=> $row['quantity'],
            'total' => (int)$row['rate'] * (int)$row['quantity'],
        ];
        return TenderMaterialWork::create($input);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $title = $event->getSheet()->getTitle();
                $exploded = explode('-', $title);
                $this->material_type_id = $exploded[0];
            }
        ];
    }


    public function rules(): array
    {
        return [
            'rate' => 'required',
            'quantity'=> 'required',

        ];
    }
}
