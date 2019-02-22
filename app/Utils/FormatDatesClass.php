<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 13.02.2019
 * Time: 13:18
 */

namespace App\Utils;


class FormatDatesClass
{

    private $formatDate;

    public function __construct($formatDate)
    {
        $this->formatDate = $formatDate;
    }

    /**
     * Преобразование полей дат в нужный формат
     *
     * @param $arr {array}
     * @param $keys {array|string}
     * @return mixed
     */
    public function formatDatesFields($arr, $keys)
    {
        if (is_array($arr) && is_string($keys)) {
            foreach ($arr as $item) {
                $item->{$keys} = date($this->formatDate, strtotime($item->{$keys}));
            }
        }

        if (is_array($arr) && is_array($keys)) {
            foreach ($arr as $value => $item) {
                foreach ($keys as $elem) {
                    if (is_object($item)) {
                        if ($this->isDate($item->{$elem})) {
                            $item->{$elem} = date($this->formatDate, strtotime($item->{$elem}));
                        }
                    } else {
                        if ($this->isDate($arr[$elem])) {
                            $arr[$elem] = date($this->formatDate, strtotime($arr[$elem]));
                        }
                    }

                }

            }
        }

        return $arr;
    }

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

    /**
     * Преобразование даты в нужный формат для сохранения в базе
     *
     * @param {array} $arr
     * @return mixed
     */
    public function formatServerDate($date)
    {
        return date("Y-m-d H:i:s", strtotime($date));
    }
}
