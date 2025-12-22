<?php

namespace App\Exports;

use App\Models\MaterialWorkType;
use App\Models\ProjectSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ProjectScheduleExport implements WithColumnFormatting,
                                        WithMapping,
                                        Responsable,
                                        ShouldAutoSize,
                                        WithHeadings,
                                        WithEvents,
                                        FromCollection
{
    use Exportable;

    /**
     * @var bool
     */
    private $isDemo;

    /**
     * @param int $question
     * @param bool $demo
     */
    public function __construct(array $request, $item = null)
    {
        if ($request['demo']) {
            $this->isDemo = true;
        }
    }

    /**
    * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function collection()
    {
        return MaterialWorkType::get();
    }

    public function map($projectSchedule): array
    {
        return [
            "",
            "",
            "",
            "",
        ];
    }

    public function headings(): array {
        return [
            '#',
            'Task',
            'Start Date',
            'End Date',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:AD1')->applyFromArray([
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


    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
