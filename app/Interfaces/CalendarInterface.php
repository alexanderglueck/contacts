<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 03.02.2018
 * Time: 13:58
 */

namespace App\Interfaces;


interface CalendarInterface
{
    public function getCalculatedName($year);

    public function getCalendarEventUrl($contact);

    public static function datesInRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate);

}