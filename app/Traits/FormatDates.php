<?php

namespace App\Traits;

trait FormatDates
{
    /**
     * Преобразование даты в нужный формат
     *
     * @param {array} $arr
     * @return mixed
     */
    public function needFormatDate($arr)
    {
        foreach ($arr as $item) {
            $item->created_at = date("d-m-Y", strtotime($item->created_at));
        }

        return $arr;
    }
}
