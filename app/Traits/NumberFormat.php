<?php

namespace App\Traits;

trait NumberFormat
{
    /**
     * Форматирование чисел
     *
     * @param $arr
     * @return mixed
     */
    public function prettyFormat($arr)
    {
        foreach ($arr as $item) {
            foreach ($item as $key => $value) {
                if (is_numeric($value)) {
                    $item->{$key} = number_format($item->{$key}, 0, ',', ' ');
                }
            }
        }
        return $arr;
    }
}
