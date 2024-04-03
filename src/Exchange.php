<?php

namespace Brunoinds\ParaguayDolarLaravel;


use DateTime;
use Brunoinds\ParaguayDolarLaravel\ExchangeDate\ExchangeDate;
use Brunoinds\ParaguayDolarLaravel\Store\Store;
use Brunoinds\ParaguayDolarLaravel\Converter\Converter;

class Exchange{
    public static function on(DateTime $date): ExchangeDate
    {
        return new ExchangeDate($date);
    }
    public static function now():ExchangeDate{
        return new ExchangeDate(new DateTime());
    }
    public static function useStore(Store $store) :void
    {
        Converter::$store = $store;
    }
}