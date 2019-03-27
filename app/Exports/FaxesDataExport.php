<?php

namespace App\Exports;

use App\FaxesMoreInfos;
use Maatwebsite\Excel\Concerns\FromCollection;

class FaxesDataExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return FaxesMoreInfos::all();
    }
}
