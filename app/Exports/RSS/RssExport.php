<?php

namespace App\Exports\RSS;

use Maatwebsite\Excel\Concerns\FromArray;

class RssExport implements FromArray
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return $this->data;
    }
}
