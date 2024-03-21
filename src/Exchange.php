<?php

namespace Brunoinds\ParaguayDolarLaravel;


use DateTime;
use Brunoinds\ParaguayDolarLaravel\ExchangeDate\ExchangeDate;

class Exchange{
    public static function on(DateTime $date): ExchangeDate
    {
        return new ExchangeDate($date);
    }
    public static function now():ExchangeDate{
        return new ExchangeDate(new DateTime());
    }
}