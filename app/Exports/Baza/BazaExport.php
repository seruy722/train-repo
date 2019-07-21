<?php

namespace App\Exports\Baza;

use App\Baza;
use Maatwebsite\Excel\Concerns\FromCollection;

class BazaExport implements FromCollection
{
    protected $items;
    public function __construct(array $itemsIds)
    {
        $this->items = $itemsIds;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Baza::whereIn('id', $this->items)->get();
    }
}
