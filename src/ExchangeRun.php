<?php
namespace Brunoinds\ParaguayDolarLaravel;

//Require composer autoload
require __DIR__ . '/../vendor/autoload.php';

use Brunoinds\ParaguayDolarLaravel\Enums\Currency;
use Brunoinds\ParaguayDolarLaravel\Exchange;
use DateTime;

$result = Exchange::on(DateTime::createFromFormat('Y-m-d', '2023-12-10'))->convert(Currency::USD, 1)->to(Currency::PYG);
var_dump($result);


$date = DateTime::createFromFormat('Y-m-d', '2023-12-10');

$result = Exchange::on($date)
                    ->convert(Currency::USD, 1)
                    ->to(Currency::PYG);

echo $result; // 0.27


Exchange::now()->convert(Currency::USD, 1)->to(Currency::PYG); // 0.32