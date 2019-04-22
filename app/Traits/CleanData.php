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

    public function cleanArrayObjects(array $arr){
        $cleanArr = [];
        foreach ($arr as $item) {
            $clean = array_map('trim', $item);
            $clean = array_map('strip_tags', $clean);
            $clean = array_map('stripcslashes', $clean);
            $cleanArr[] = $clean;
        }
        return $cleanArr;
    }
}
