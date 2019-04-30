<?php

namespace App\Exports\Fax;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxesDataExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $headers;
    protected $faxID;
    protected $categories;

    public function __construct(array $data, array $headers, int $faxID, array $categories)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->faxID = $faxID;
        $this->categories = $categories;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxCommonData($this->faxID, $this->categories);
        $sheets[] = new FaxExport($this->data, $this->headers, $this->faxID, $this->categories);
        return $sheets;
    }
}
