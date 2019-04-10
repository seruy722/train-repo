<?php

namespace App\Exports;

use App\FaxesMoreInfos;
use App\Cargo;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FaxesDataExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $countEntries;
    protected $sumPlaces;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = DB::table('faxes_more_info')
            ->join('users', 'faxes_more_info.client_id', '=', 'users.id')
            ->select('users.name', 'faxes_more_info.place', 'faxes_more_info.kg', 'faxes_more_info.for_kg', 'faxes_more_info.for_place')
            ->orderBy('name')
            ->get();
        $this->countEntries = $data->count() + 2;
        $this->sumPlaces = $data->sum('place');
        return $data;
    }

    public function headings(): array
    {
        return [
            'Клиент',
            'Мест',
            'Вес',
            '',
            '',
            'Сумма'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $arr = [
                    'borders' => [
//                    'outline' => [
//                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
//                        'color' => ['argb' => 'FFFF0000'],
//                    ],
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                            'color' => ['argb' => 'FFFF0000'],
                        )
                    ]
                ];

                $cellRange = 'A1:F' . $this->countEntries; // All
                $cellRangeHeaders = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getCell('B' . $this->countEntries)->setValue($this->sumPlaces); // set value in cell
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setBold(500);

//                $event->sheet->styleCells(
//                    'B2:G8',
//                    [
//                        'borders' => [
//                            'outline' => [
//                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
//                                'color' => ['argb' => 'FFFF0000'],
//                            ],
//                        ]
//                    ]
//                );
            },
        ];
    }
}
