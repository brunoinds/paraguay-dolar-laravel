<?php

namespace Brunoinds\ParaguayDolarLaravel\Converter;

use Illuminate\Support\Facades\Cache;
use DateTime;
use Carbon\Carbon;


class Converter{
    public static function convertFromTo(DateTime $date, float $amount, string $from, string $to){
        if ($from === $to){
            return $amount;
        }

        if ($from === 'USD'){
            return $amount * Converter::fetchExchangeRates($date, $from);
        }else if ($to === 'USD'){
            return $amount / Converter::fetchExchangeRates($date, $to);
        }
    }
    private static function fetchExchangeRates(DateTime $date, string $to){
        if ($date->format('Y-m-d') > Carbon::now()->timezone('America/Lima')->format('Y-m-d')){
            $date = Carbon::now()->timezone('America/Lima')->toDateTime();
        }

        $dateInfo = Carbon::createFromDate($date);

        if ($dateInfo->isFuture()){
            $dateInfo = Carbon::now()->timezone('America/Lima');
        }
        if ($dateInfo->isToday()){
            $dateInfo = $dateInfo->subDays(1);
        }
        if ($dateInfo->isWeekend()){
            if ($dateInfo->isSunday()){
                $dateInfo->subDays(2);
            }else{
                $dateInfo->subDays(1);
            }
        }

        $date = $dateInfo->toDateTime();

        $dateString = $date->format('Y-m-d');

        $curl = curl_init();

        $stores = [];
        $cachedValue = Cache::store('file')->get('Brunoinds/ParaguayDolarLaravelStore');
        if ($cachedValue){
            $stores = json_decode($cachedValue, true);
            if (isset($stores[$dateString])){
                return $stores[$dateString];
            }
        }


        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.bcp.gov.py/webapps/web/cotizacion/monedas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',

        ]);

        //Set timeout of 5 seconds:
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        

        //set post form fields:
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'fecha=' . $date->format('d/m/Y'));

        $response = curl_exec($curl);
        curl_close($curl);

        //Check response code is 200:
        if ($response === false) {
            throw new \Exception('Failed to fetch exchange rates from Banco Central de Paraguay API: ' . curl_error($curl));
        }

        $currenciesTable = [];

        //Convert to DOM:
        $dom = new \DOMDocument();
        @$dom->loadHTML($response);
        $table = $dom->getElementById('cotizacion-interbancaria');

        $i = 0;
        foreach ($table->getElementsByTagName('tr') as $row) {
            if ($i >= 22 || $i < 2){
                $i++;
                continue;
            }else{
                $i++;
            }
            $tds = $row->getElementsByTagName('td');
            $currency = $tds[1]->nodeValue;

            $value = $tds[3]->nodeValue;
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            $value = floatval($value);
            $currenciesTable[$currency] = $value;
        }

        $rate = $currenciesTable[$to];

        $rate = $currenciesTable[$to];

        $stores[$dateString] = $rate;
        Cache::store('file')->put('Brunoinds/ParaguayDolarLaravelStore', json_encode($stores));

        return $rate;
    }

}