<?php

namespace Brunoinds\ParaguayDolarLaravel\ExchangeDate;

use Brunoinds\ParaguayDolarLaravel\Converter\Converter;
use Brunoinds\ParaguayDolarLaravel\Enums\Currency;
use Brunoinds\ParaguayDolarLaravel\ExchangeTransaction\ExchangeTransaction;
use DateTime;
use Brunoinds\ParaguayDolarLaravel\Store\Store;

class ExchangeDate{
    public DateTime $date;

    public function __construct(DateTime $date){
        if (!Converter::$store){
            Converter::$store = Store::newFromLaravelCache();
        }
        $this->date = $date;
    }

    public function convert(Currency $currency, float $amount): ExchangeTransaction{
        return new ExchangeTransaction($this, $currency, $amount);
    }
}