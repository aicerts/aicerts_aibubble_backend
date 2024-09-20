<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class filterSymbolController extends Controller
{
    public  function sleepMillis($ms) {
        return usleep($ms * 1000);
    }


    public function getIntradayData($symbol)
    {
        $apiKey = env('MARKET_API_KEY');
        $url = "http://api.marketstack.com/v1/intraday";
        
        try {
            $response = Http::get($url, [
                'access_key' => $apiKey,
                'symbols' => $symbol,
                'interval' => '1min',
                'limit' => 1,
            ]);
            
            if ($response->successful()) {
                return $response->json('data');
            } else {

                Log::error("Error fetching intraday data for $symbol: {$response->body()}");
                return [];
            }
        } catch (\Exception $e) {
            Log::error("Exception fetching intraday data for $symbol: {$e->getMessage()}");
            return [];
        }
    }


    public function processSymbols(array $symbols) {
        $noData = [];
        $sleepTime = 500000;
    
        foreach ($symbols as $element) {
            $result = $this->getIntradayData($element['symbol']);
            
            usleep($sleepTime);
    
            Log::info("Symbol: {$element['symbol']}, Result Length: " . count($result));
            if (count($result) === 0) {
                $noData[] = $element['name'];
            }
        }

        return [
            'total_symbols' => count($symbols),
            'no_data_count' => count($noData),
            'no_data_names' => $noData,
        ];
    }
}
