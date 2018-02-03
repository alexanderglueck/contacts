<?php

namespace App\Interfaces;

use DateTimeInterface;

interface CalendarInterface
{
    public function getCalculatedName($year);

    public function getCalendarEventUrl();

    public static function datesInRange(DateTimeInterface $startDate, DateTimeInterface $endDate);
}