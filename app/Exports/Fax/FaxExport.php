<?php

namespace App\Exports\Fax;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Traits\CleanData;

class FaxExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle, WithMapping
{
    use CleanData;

    protected $data;
    protected $countEntries;
    protected $countPlaces;
    protected $countKg;
    protected $countSum;
    protected $headers;
    protected $alphabet;
    protected $brands;
    protected $sequence;
    protected $clientSheetTitle = 'Клиенты';
    protected $yellow = 'FFFF00';
    protected $green = '92D050';
    protected $red = 'FF0000';
    protected $faxID;
    // ТАБЛИЦА КАТЕГОРИЙ
    protected $categories;
    protected $totalCategoriesSum;

    public function __construct(array $data, array $headers, int $faxID, array $categories)
    {
        $this->data = $this->cleanArrayObjects($data);
        $this->sequence = ['name' => 'Клиент', 'place' => 'Мест', 'kg' => 'Вес', 'for_kg' => 'За кг', 'for_place' => 'За место', 'sum' => 'Сумма'];
        $this->headers = $this->clean($headers['headers']);
//        $this->brands = $brands;
        $this->brands = $this->getClientsNamesWithBrands($data);
        $this->countPlaces = $this->count($data, 'place');
        $this->countKg = $this->count($data, 'kg');
        $this->countEntries = $this->countEntries($data);
        $this->countSum = $this->count($data, 'sum');
        $this->alphabet = range('A', 'Z');
//        $this->faxID = $faxID;
//        $this->categories = $this->getCategoriesFromDB();
        $this->categories = $this->countCategoryKg($categories);
        $this->totalCategoriesSum = $this->count($this->categories, 'sum');
        if (count($this->categories) === 0) {
            array_splice($this->headers, 3, 2);
            array_splice($this->sequence, 3, 2);
        }
    }

    public function countCategoryKg(array $categories)
    {
//        $this->footerCategories = array_slice($categories, -3, 1);
        array_splice($categories, -3);
        $categorySequence = ['for_kg' => 0, 'category_name' => '', 'place' => 0, 'kg' => 0, 'sum' => 0];
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

    public function getClientsNamesWithBrands(array $data)
    {
        $newArr = [];
        foreach ($data as $item) {
            $cleanItem = $this->clean($item);
            if (array_key_exists('name', $cleanItem) && array_key_exists('kg', $cleanItem) && array_key_exists('place', $cleanItem)) {
                if (array_key_exists('brand', $cleanItem) && strval($cleanItem['brand']) === '1') {
                    $newArr[] = ['name' => $cleanItem['name'], 'kg' => $cleanItem['kg'], 'place' => $cleanItem['place']];
                }
            }
        }
        return $newArr;
    }

    public function check()
    {
        return $this->categories;
    }

    public function countHeaders(array $headers)
    {
        return count($headers);
    }

    private function count(array $arr, string $field)
    {
        $count = 0;
        foreach ($arr as $item) {
            if (array_key_exists($field, $item)) {
                $count += $item[$field];
            } else {
                break;
            }
        }
        return $count;
    }

    // Количество записей + headers + новая строка
    private function countEntries(array $arr)
    {
        return count($arr) + 2;
    }

    public function title(): string
    {
        return $this->clientSheetTitle;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function map($item): array
    {
        $arr = [];

        foreach ($this->sequence as $key => $value) {
            if (array_key_exists($key, $item)) {
                $arr[] = $item[$key];
            }
        }
        return $arr;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRangeHeaders = 'A1:N1'; // HEADERS
                $cellForSum = $this->alphabet[$this->countHeaders($this->headers) - 1] . $this->countEntries; // SUM CELL
                $cellRange = 'A1:' . $this->alphabet[$this->countHeaders($this->headers) - 1] . $this->countEntries; // All RANGE
                $cellRangeCounts = 'A' . $this->countEntries . ':' . $cellForSum; // FOOTER TOTAL COUNTS
                $keyPlace = array_search('Мест', $this->headers);
                $keyKg = array_search('Вес', $this->headers);

                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                // МЕСТ
                if (in_array('Мест', $this->headers)) {
//                    $key = array_search('Мест', $this->headers);
                    $cellForCountPlaces = $this->alphabet[$keyPlace] . $this->countEntries; // COUNT PLACES CELL
                    $event->sheet->getDelegate()->getCell($cellForCountPlaces)->setValue($this->countPlaces); // set value in cell
                }

                // ВЕС
                if (in_array('Вес', $this->headers)) {
//                    $key = array_search('Вес', $this->headers);
                    $cellForCountKg = $this->alphabet[$keyKg] . $this->countEntries; // COUNT KG CELL
                    $event->sheet->getDelegate()->getCell($cellForCountKg)->setValue($this->countKg); // set value in cell
                }

                // СУММА
                if (in_array('Сумма', $this->headers)) {
                    $event->sheet->getDelegate()->getCell($cellForSum)->setValue($this->countSum); // set value in cell
                    $event->sheet->getDelegate()->getStyle($cellForSum)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($this->yellow);
                }

                // За кг
                if (in_array('За кг', $this->headers)) {
                    $key = array_search('За кг', $this->headers);
                    $cellForCountKg = $this->alphabet[$key] . '2:' . $this->alphabet[$key] . $this->countEntries; // COUNT KG CELL
                    $event->sheet->getDelegate()->getStyle($cellForCountKg)->getFont()->setBold(500)->getColor()->applyFromArray(array('rgb' => $this->red));
                }

                // За место
                if (in_array('За место', $this->headers)) {
                    $key = array_search('За место', $this->headers);
                    $cellForCountKg = $this->alphabet[$key] . '2:' . $this->alphabet[$key] . $this->countEntries; // COUNT KG CELL
                    $event->sheet->getDelegate()->getStyle($cellForCountKg)->getFont()->setBold(500)->getColor()->applyFromArray(array('rgb' => $this->red));
                }


                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRangeCounts)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));

                if ($this->countHeaders($this->headers) && $this->countHeaders($this->headers) === 6) {
                    // CATEGORIES
                    $cellForTotalCategoriesSum = 'E' . ($this->countEntries + 4 + count($this->categories));
                    $cellForKgCategories = 'D' . ($this->countEntries + 4 + count($this->categories));
                    $cellForPlaceCategories = 'C' . ($this->countEntries + 4 + count($this->categories));
                    $cellForTotalCategoriesSumUnderTotalSum = $this->alphabet[$this->countHeaders($this->headers) - 1] . ($this->countEntries + 2);
                    $totalSumDifference = $this->alphabet[$this->countHeaders($this->headers)] . ($this->countEntries + 1);
                    $cellRangeForBorders = 'B' . ($this->countEntries + 4) . ':' . 'E' . ($this->countEntries + 4 + count($this->categories));
                    $cellRangeFooter = 'B' . ($this->countEntries + 4 + count($this->categories)) . ':' . 'E' . ($this->countEntries + 4 + count($this->categories));

                    $arrCat = $this->categories;
                    array_unshift($arrCat, array(''), array(''), array(''));
                    $event->sheet->appendRows($arrCat, $event);
                    $event->sheet->getDelegate()->getCell($cellForTotalCategoriesSum)->setValue($this->totalCategoriesSum);
                    if (count($this->categories) !== 0) {
                        $event->sheet->getDelegate()->getCell($totalSumDifference)->setValue($this->countSum - $this->totalCategoriesSum);
                        $event->sheet->getDelegate()->getStyle($totalSumDifference)->getFont()->setBold(500)->setSize(14);
                        $event->sheet->getDelegate()->getStyle($totalSumDifference)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                        $event->sheet->getDelegate()->getStyle($totalSumDifference)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($this->green);
                        $event->sheet->getDelegate()->getStyle($totalSumDifference)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                        $event->sheet->getDelegate()->getCell($cellForTotalCategoriesSumUnderTotalSum)->setValue($this->totalCategoriesSum);
                        $event->sheet->getDelegate()->getStyle($cellForTotalCategoriesSumUnderTotalSum)->getFont()->setBold(500)->setSize(14);
                        $event->sheet->getDelegate()->getStyle($cellForTotalCategoriesSumUnderTotalSum)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                        $event->sheet->getDelegate()->getStyle($cellForTotalCategoriesSumUnderTotalSum)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($this->yellow);
                        $event->sheet->getDelegate()->getStyle($cellForTotalCategoriesSumUnderTotalSum)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));


                        $event->sheet->getDelegate()->getCell($cellForKgCategories)->setValue($this->countKg);
                        $event->sheet->getDelegate()->getCell($cellForPlaceCategories)->setValue($this->countPlaces);
                        $event->sheet->getDelegate()->getStyle($cellRangeFooter)->getFont()->setBold(500);
                        $event->sheet->getDelegate()->getStyle($cellRangeForBorders)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                    }


                }

                // TABLE WITHOUT FOR_KG AND FOR_PLACE FIELDS
                if (count($this->categories) !== 0) {
                    $cellRangeHeaders2 = 'A' . (count($this->categories) + $this->countEntries + 8) . ':N' . (count($this->categories) + $this->countEntries + 8);
                    $cellForCountPlaces2 = $this->alphabet[$keyPlace] . ($this->countEntries + count($this->categories) + 7 + $this->countEntries);
                    $cellForCountKg2 = $this->alphabet[$keyKg] . ($this->countEntries + count($this->categories) + 7 + $this->countEntries);
                    $cellRange2 = 'A' . (count($this->categories) + $this->countEntries + 8) . ':D' . ($this->countEntries + count($this->categories) + 7 + $this->countEntries);
                    $data = $this->data;
                    $newData = [];
                    foreach ($data as $item) {
                        $arr = [];
                        foreach ($this->sequence as $key => $elem) {
                            if ($key === 'for_kg' || $key === 'for_place') {
                                continue;
                            }
                            array_push($arr, $item[$key]);
                        }
                        array_push($newData, $arr);
                    }
                    unset($this->sequence['for_kg'], $this->sequence['for_place']);
                    array_unshift($newData, array(''), array(''), array(''), $this->sequence);
                    $event->sheet->appendRows($newData, $event);

                    $cellForSum2 = $this->alphabet[$this->countHeaders($this->sequence) - 1] . ($this->countEntries + count($this->categories) + 7 + $this->countEntries);
                    $cellRangeFooter = 'A' . (count($this->categories) + $this->countEntries + 7 + $this->countEntries) . ':' . $cellForSum2;
                    $event->sheet->getDelegate()->getStyle($cellRangeHeaders2)->getFont()->setBold(500);
                    $event->sheet->getDelegate()->getStyle($cellRangeFooter)->getFont()->setBold(500);
                    $event->sheet->getDelegate()->getCell($cellForCountPlaces2)->setValue($this->countPlaces);
                    $event->sheet->getDelegate()->getCell($cellForCountKg2)->setValue($this->countKg);
                    $event->sheet->getDelegate()->getStyle($cellForSum2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($this->yellow);
                    $event->sheet->getDelegate()->getCell($cellForSum2)->setValue($this->countSum);
                    $event->sheet->getDelegate()->getStyle($cellRange2)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                    $event->sheet->getDelegate()->getStyle($cellRange2)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                    $event->sheet->getDelegate()->getStyle($cellRange2)->getFont()->setSize(14);
                }


                // Выделение брендовых клиетов жирным шрифтом
                if (count($this->brands) > 0) {
                    $rows = $event->sheet->getDelegate()->toArray();
                    foreach ($rows as $key => $value) {
                        $cleanValue = $this->clean($value);
                        foreach ($this->brands as $brand) {
                            if ($cleanValue[0] === $brand['name'] && $brand['kg'] === $cleanValue[2] && $brand['place'] === $cleanValue[1]) {
                                $event->sheet->getDelegate()->getStyle('A' . ($key + 1) . ':W' . ($key + 1))->getFont()->setBold(500);
                            }
                        }
                    }
                }
            },
        ];
    }
}
