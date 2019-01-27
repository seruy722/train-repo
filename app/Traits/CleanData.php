<?php

namespace App\Traits;

trait CleanData
{
    /**
     * Форматирование массива данных
     *
     * @param $value
     * @return array
     */
    public function clean($value)
    {
        $arr = array_map("trim", $value);
        $arr = array_map("strip_tags", $arr);
        $arr = array_map("stripcslashes", $arr);
        return $arr;
    }
}
