<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxesImport implements WithMultipleSheets
{

    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }
}
