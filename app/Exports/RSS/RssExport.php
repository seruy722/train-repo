<?php

namespace App\Exports\RSS;

use App\Cargo;
use Maatwebsite\Excel\Concerns\FromCollection;

class RssExport implements FromCollection
{
//    protected $data;
//
//    public function __construct(array $data)
//    {
//        $this->data = $data;
//    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Cargo::all();
    }
}
