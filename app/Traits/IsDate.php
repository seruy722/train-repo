<?php

namespace App\Traits;

trait IsDate
{
    /**
     * Проверяет валидна дата или нет
     *
     * @param $value
     * @return bool
     */
    function isDate($value)
    {
        if (!$value) {
            return false;
        }

        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
