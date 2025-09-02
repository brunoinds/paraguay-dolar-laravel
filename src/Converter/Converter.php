<?php

namespace Brunoinds\ParaguayDolarLaravel\Converter;

use DateTime;
use Carbon\Carbon;
use Brunoinds\ParaguayDolarLaravel\Store\Store;


class Converter{
    public static Store|null $store = null;

    public static function convertFromTo(DateTime $date, float $amount, string $from, string $to)
    {
        if ($from === $to){
            return $amount;
        }

        return Converter::fetchConvertion($date, $amount, $from, $to);
    }

    private static function fetchConvertion(DateTime $date, float $amount, string $from, string $to)
    {
        if ($date->format('Y-m-d') > Carbon::now()->timezone('America/Lima')->format('Y-m-d')){
            $date = Carbon::now()->timezone('America/Lima')->toDateTime();
        }

        $dateString = $date->format('Y-m-d');

        $curl = curl_init();

        // Build the API URL based on whether it's historical or current conversion
        $apiKey = env('CURRENCY_GETGEO_API_KEY');
        if (!$apiKey) {
            throw new \Exception('CURRENCY_GETGEO_API_KEY environment variable is not set');
        }

        $curlURL = 'https://api.getgeoapi.com/v2/currency/convert?' . http_build_query([
            'api_key' => $apiKey,
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'format' => 'json'
        ]);

        // For historical data, use the historical endpoint
        if ($dateString !== Carbon::now()->timezone('America/Lima')->format('Y-m-d')) {
            $curlURL = 'https://api.getgeoapi.com/v2/currency/historical/' . $dateString . '?' . http_build_query([
                'api_key' => $apiKey,
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'format' => 'json'
            ]);
        }

        $stores = [];
        $cachedValue = Converter::$store->get();
        if ($cachedValue){
            $stores = json_decode($cachedValue, true);
            if (isset($stores[$curlURL])){
                return $stores[$curlURL];
            }
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $curlURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false) {
            throw new \Exception('Failed to fetch exchange rates from GetGeoAPI: ' . curl_error($curl));
        }

        $results = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response: "' . json_last_error_msg(). '". The API response was: ' . $response);
        }

        // Check if the API returned an error
        if (isset($results['status']) && $results['status'] === 'failed') {
            $errorMessage = isset($results['error']['message']) ? $results['error']['message'] : 'Unknown error';
            $errorCode = isset($results['error']['code']) ? $results['error']['code'] : 'Unknown';
            throw new \Exception('GetGeoAPI error (' . $errorCode . '): ' . $errorMessage);
        }

        // Check if the response has the expected structure
        if (!isset($results['rates']) || !isset($results['rates'][$to])) {
            throw new \Exception('Invalid API response structure. Expected rates for ' . $to . '. The API response was: ' . $response);
        }

        try {
            $rate = $results['rates'][$to]['rate_for_amount'];
            $stores[$curlURL] = $rate;
        } catch (\Throwable $th) {
            throw new \Exception('Error while parsing the conversion rate. The API response was: ' . $response);
        }

        try {
            Converter::$store->set(json_encode($stores));
            return $rate;
        } catch (\Throwable $th) {
            throw new \Exception('Error while storing the conversion rate. If you are using a custom adapter, please make sure it is working correctly. The API response was: ' . $response);
        }
    }
}
