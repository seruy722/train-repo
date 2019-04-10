<?php

namespace App\Traits;

trait FormatDate
{
    public function formatDateToMySqlDate($date)
    {
        if ($date) {
            return (new \DateTime($date))->format('Y-m-d H:i:s');
        }
        return $date;
    }

    public function formatDateWithServerTimezone($date)
    {
        if ($date) {
            return date("c", strtotime($date));
        }
        return $date;
    }

    public function formatDateForSaveFile(){
        return (new \DateTime())->format('Y-m-d H.i.s.v');
    }
}
