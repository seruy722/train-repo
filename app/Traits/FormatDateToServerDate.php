<?php

namespace App\Traits;

trait FormatDateToServerDate
{
    /**
     * Преобразование даты в нужный формат
     *
     * @param {array} $arr
     * @return mixed
     */
    public function formatServerDate($date)
    {

        $serverTime = date('H:i:s');
        $fullDate = $date . ' ' . $serverTime;
        return date("Y-m-d H:i:s", strtotime($fullDate));
    }
}
