<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class FaxCommonData implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $headers = ['Код', 'Клиент', 'Мест', 'Вес', 'Магазин', 'Опись вложения','Город/клиента'];
    protected $faxID;
    protected $data;
    protected $countEntries;
    protected $sumPlace;
    protected $sumKg;
    protected $categories;
    protected $moreEntries = 2;


    public function __construct(int $faxID, array $categories)
    {
        $this->faxID = $faxID;
        $this->data = $this->getDataFromTable();
        $this->countEntries = $this->countEntries();
        $this->sumPlace = $this->countSum($this->data, 'place');
        $this->sumKg = $this->countSum($this->data, 'kg');
        $this->categories = $this->deleteEntriesFromCategories($categories);
    }

    public function deleteEntriesFromCategories(array $categories){
        array_splice($categories, -3);
        $categorySequence = ['category_name' => '', 'place' => 0, 'kg' => 0];
        $newArr = [];
        foreach ($categories as $category) {
            foreach ($category as $key => $item) {
                if (array_key_exists($key, $categorySequence)) {
                    $categorySequence[$key] = $category[$key];
                }
            }
            array_push($newArr, $categorySequence);
        }

        return $newArr;
    }

    public function getDataFromTable(){
        $dataTable =  DB::table('faxes_more_info')
            ->where('fax_id', $this->faxID)
            ->join('users', 'faxes_more_info.client_id', '=', 'users.id')
            ->select('faxes_more_info.code', 'users.name', 'faxes_more_info.place', 'faxes_more_info.kg', 'faxes_more_info.shop', 'faxes_more_info.name_of_things', 'faxes_more_info.notation')
            ->get()
        ;
        $sorted = $dataTable->sortBy('name');

        return $sorted;
    }


    public function collection()
    {
        return $this->data;
    }

    public function countEntries(){
        return $this->data->count();
    }

    public function countSum($data, string $field){
        return $data->sum($field);
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function title(): string
    {
        return 'Общее';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $headerEntry = 'A1:G1';
                $footerEntry = 'A'.($this->countEntries + $this->moreEntries).':G'. ($this->countEntries + $this->moreEntries);
                $cellRange = 'A1:G' . ($this->countEntries + $this->moreEntries);
                $callPlace = 'C' . ($this->countEntries + $this->moreEntries);
                $callKg = 'D' . ($this->countEntries + $this->moreEntries);

                $event->sheet->getDelegate()->getStyle($headerEntry)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($footerEntry)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getCell($callPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getCell($callKg)->setValue($this->sumKg);

                // CATEGORIES
                $categoriesCellRange = 'A'.($this->countEntries + $this->moreEntries + 4).':C'. ($this->countEntries + $this->moreEntries + count($this->categories)+4);
                $cellCategoriesKg = 'C'. ($this->countEntries + $this->moreEntries + count($this->categories)+4);
                $cellCategoriesPlace = 'B'. ($this->countEntries + $this->moreEntries + count($this->categories) +4);
                $cellCategoriesFooter = 'A'.($this->countEntries + $this->moreEntries + count($this->categories)+4).':C'. ($this->countEntries + $this->moreEntries + count($this->categories) +4);

                $arrCat = $this->categories;
                array_unshift($arrCat, array(''), array(''), array(''));
                $event->sheet->appendRows($arrCat, $event);

                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getCell($cellCategoriesKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getCell($cellCategoriesPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellCategoriesFooter)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getFont()->setSize(10);
            }
        ];
    }

}
