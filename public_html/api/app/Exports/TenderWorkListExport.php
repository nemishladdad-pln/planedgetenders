<?php

namespace App\Exports;

use App\Http\Resources\TenderMaterialWorkResource;
use App\Models\MaterialWorkType;
use App\Models\TenderMaterialWork;
use Database\Factories\TenderMaterialWorkFactory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class TenderWorkListExport implements WithMapping,
                                        Responsable,
                                        ShouldAutoSize,
                                        WithHeadings,
                                        WithEvents,
                                        FromCollection,
                                    WithTitle
{
    use Exportable;

    /**
     * @var bool
     */
    private $isDemo;
    private $selectedItem;

    /**
     * @param int $question
     * @param bool $demo
     */
    public function __construct(array $request, $item = null)
    {
        if ($request['demo']) {
            $this->isDemo = true;
        }
        if ($item) {
            $this->selectedItem = $item;
        }
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->isDemo) {
            return TenderMaterialWork::factory()->count(3)->make();
        }
        return TenderMaterialWorkResource::collection(TenderMaterialWork::all());
    }


    public function map($subject): array
    {
        if ($this->isDemo) {
            return array();
        }
        return [
            // $subject->id,
            // json_decode($subject->label),
            // json_decode($subject->description),
            // //$subject->board_id,
            // $subject->standard_id,
            // $subject->icon,
            // $subject->tags->pluck('name'),
            // //$subject->language_id,
            // $subject->created_at,
        ];
    }

    public function headings(): array {
        if ($this->isDemo) {
            return [
                'Work',
                'Rate',
                'Unit',
                'Quantity',
            ];
        }
        return [
            '#',
            'Work',
            'Rate',
            'Unit',
            'Quantity',
            'Created At'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font'=> [
                        'bold' => true,
                    ],
                    /*'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ]*/
                ]);
            }
        ];
    }

    public function title(): string
    {
        $materialWork = MaterialWorkType::findOrFail($this->selectedItem);
        return $this->selectedItem."-".$materialWork->name;
    }
}
