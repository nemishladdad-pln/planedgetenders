<?php

namespace App\Exports;

use App\Repositories\ContractorTenderRepository;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;

class ContractorTenderExport implements FromView,
                                        ShouldAutoSize,
                                        WithColumnWidths,
                                        WithStyles
{

    use Exportable;

    private $id;

    private $response;

    private $action;

    /**
     * @param object $request
     */
    public function __construct($request)
    {
        $this->id = $request['id'];
        $contractorTenderRepository = new ContractorTenderRepository;
        $this->action = $request['action'];
        $actionName = $request['action'];
        $this->response = $contractorTenderRepository->$actionName($this->id);
    }

    public function view(): View
    {
        return view('exports.'.$this->action, [
            'response' => $this->response,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'B' => 100,
        ];
    }

    public function  styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
        $sheet->getStyle('B1:B'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
    }

    public function defaultStyles() {

    }

}
