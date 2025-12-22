<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TenderWorkMultipleExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $request;
    /**
     * @param int $question
     * @param bool $demo
     */
    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->request['selectedItem'] as $item) {
            $sheets[] = new TenderWorkListExport($this->request, $item);
        }

        return $sheets;
    }
}
