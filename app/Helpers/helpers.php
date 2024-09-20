<?php
namespace App\Helpers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Helpers
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $jsonData;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('MARKET_API_KEY');
        $this->baseUrl = 'http://api.marketstack.com/v1/';
        $this->jsonData = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);
    }

    public function getJsonData()
    {
        return $this->jsonData;
    }

    public static function convertCandles($dailyData, $interval)
    {
        $periods = [
            'weekly' => function ($date) {
                return Carbon::parse($date)->startOfWeek()->format('Y-m-d');
            },
            'monthly' => function ($date) {
                return Carbon::parse($date)->startOfMonth()->format('Y-m-d');
            },
            'yearly' => function ($date) {
                return Carbon::parse($date)->startOfYear()->format('Y-m-d');
            },
        ];

        $getPeriod = $periods[$interval] ?? null;
        if (!$getPeriod) {
            throw new \Exception('Invalid interval. Use "weekly", "monthly", or "yearly".');
        }

        $groupedData = [];

        foreach ($dailyData as $candle) {
            $date = Carbon::parse($candle['date']);
            $period = $getPeriod($date);

            if (!isset($groupedData[$period])) {
                $groupedData[$period] = [
                    'date' => $candle['date'],
                    'open' => $candle['open'],
                    'high' => $candle['high'],
                    'low' => $candle['low'],
                    'close' => $candle['close'],
                    'volume' => (float) $candle['volume'],
                ];
            } else {
                $groupedData[$period]['high'] = max($groupedData[$period]['high'], $candle['high']);
                $groupedData[$period]['low'] = min($groupedData[$period]['low'], $candle['low']);
                $groupedData[$period]['close'] = $candle['close'];
                $groupedData[$period]['volume'] += (float) $candle['volume'];
            }
        }

        uasort($groupedData, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_values($groupedData);
    }

    public static function getEODData($symbol)
    {
        $apiKey = env('MARKET_API_KEY');
        $url = "http://api.marketstack.com/v1/eod?access_key={$apiKey}&symbols={$symbol}&limit=366"; // Adjust limit as needed

        try {
            $response = Http::withoutVerifying()->get($url);
            return $response->json()['data'];
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public static function getIntradayData($symbol, $interval = '1min', $limit = '61')
    {
        $apiKey = env('MARKET_API_KEY');
        $url = "http://api.marketstack.com/v1/intraday?access_key={$apiKey}&symbols={$symbol}&interval={$interval}&limit={$limit}";

        try {
            $response = Http::withoutVerifying()->get($url);
            return $response->json()['data'];
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public static function calculateChange($oldPrice, $newPrice)
    {
        if (is_numeric($oldPrice) && is_numeric($newPrice) && $oldPrice > 0) {
            return (($newPrice - $oldPrice) / $oldPrice) * 100;
        }
        return 0;
    }

    public static function getPriceEOD($symbol)
    {
        $eodData = self::getEODData($symbol);

        if (empty($eodData)) {
            return [
                'performance' => [
                    'day' => 0,
                    'week' => 0,
                    'month' => 0,
                    'year' => 0,
                ],
                'price' => 0,
                'volume' => 0,
                'volumeWeekly' => 0,
            ];
        }

        $latestEODPrice = $eodData[0]['close'];
        $latestEODVolume = $eodData[0]['volume'];
        $reversed = array_reverse($eodData);
        $weekly = self::convertCandles($reversed, 'weekly');
        $monthly = self::convertCandles($reversed, 'monthly');
        $yearly = self::convertCandles($reversed, 'yearly');

        $findEODPrice = function ($daysAgo) use ($eodData) {
            return $eodData[$daysAgo]['close'] ?? null;
        };

        $performance = [
            'day' => self::calculateChange($findEODPrice(1), $latestEODPrice),
            'week' => self::calculateChange($weekly[1]['close'], $weekly[0]['close']),
            'month' => self::calculateChange($monthly[1]['close'], $monthly[0]['close']),
            'year' => self::calculateChange($yearly[1]['close'], $yearly[0]['close']),
        ];

        return [
            'symbol' => $symbol,
            'performance' => $performance,
            'price' => $latestEODPrice,
            'volume' => $latestEODVolume,
            'volumeWeekly' => $weekly[0]['volume'],
        ];
    }

    public static function getPriceIntraday($symbol)
    {
        $intradayData = self::getIntradayData($symbol);

        if (count($intradayData) === 0) {
            return [
                'performance' => [
                    'hour' => 0,
                    'min1' => 0,
                    'min5' => 0,
                    'min15' => 0,
                ],
                'price' => 0,
            ];
        }

        $latestIntradayPrice = $intradayData[0]['last'];

        $findIntradayPrice = function ($minutesAgo) use ($intradayData) {
            return $intradayData[$minutesAgo]['last'] ?? null;
        };

        $performance = [
            'hour' => self::calculateChange($findIntradayPrice(60), $latestIntradayPrice),
            'min1' => self::calculateChange($findIntradayPrice(1), $latestIntradayPrice),
            'min5' => self::calculateChange($findIntradayPrice(5), $latestIntradayPrice),
            'min15' => self::calculateChange($findIntradayPrice(15), $latestIntradayPrice),
        ];

        return [
            'symbol' => $symbol,
            'performance' => $performance,
            'price' => $latestIntradayPrice,
        ];
    }

    public static function getPricesForIntraday($jsonData)
    {
        $updatedJsonData = [];

        foreach ($jsonData as $item) {
            $symbol = $item['symbol'];
            $intradayResult = self::getPriceIntraday($symbol);

            $item['performance'] = array_merge($item['performance'], $intradayResult['performance']);
            $item['price'] = $intradayResult['price'];

            $updatedJsonData[] = $item;
        }
        return $updatedJsonData;
    }

    public static function isIntraDay()
    {
        $now = Carbon::now('America/New_York');

        if ($now->isWeekend()) {
            return false;
        }

        $start = $now->copy()->setTime(9, 15);
        $end = $now->copy()->setTime(16, 15);

        return $now->between($start, $end, true);
    }

    public static function intradayUpdate()
    {
        Log::info('Intraday Cron Started');
        $jsonData = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);

        $jsonData = self::getPricesForIntraday($jsonData);

        usort($jsonData, function ($a, $b) {
            return $b['volume'] <=> $a['volume'];
        });

        foreach ($jsonData as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        $path = public_path('stocks/marketstack.json');
        File::put($path, json_encode($jsonData));
    }

    public function getPricesForEOD()
    {
        $updatedJsonData = [];

        foreach ($this->jsonData as $item) {
            $symbol = $item['symbol'];
            $eodResult = self::getPriceEOD($symbol);

            $item['performance'] = array_merge($item['performance'], $eodResult['performance']);
            $item['price'] = $eodResult['price'];
            $item['volume'] = $eodResult['volume'];
            $item['volumeWeekly'] = $eodResult['volumeWeekly'];

            $updatedJsonData[] = $item;
        }

        return $updatedJsonData;
    }

    public static function eodUpdate()
    {
        Log::info('Refreshing EOD data');

        $jsonData = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);

        $jsonData = (new self())->getPricesForEOD();

        usort($jsonData, function ($a, $b) {
            return $b['volume'] <=> $a['volume'];
        });

        foreach ($jsonData as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        $path = public_path('stocks/marketstack.json');
        File::put($path, json_encode($jsonData));

        Log::info('EOD data updated successfully');
    }

    public static function add_new_stock_symbol($symbol,$symbol_name)
    {
        $symbols = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);
        $highestSymbol = collect($symbols)->sortByDesc('id')->first();
        $highestId = ($highestSymbol['id']+1);

        $new_item = [];
        $intradayData = self::getIntradayData($symbol);

        if (count($intradayData) === 0) {
            return [
            ];
        }

        $latestIntradayPrice = $intradayData[0]['last'];

        $findIntradayPrice = function ($minutesAgo) use ($intradayData) {
            return $intradayData[$minutesAgo]['last'] ?? null;
        };

        $performance_intraday = [
            'hour' => self::calculateChange($findIntradayPrice(60), $latestIntradayPrice),
            'min1' => self::calculateChange($findIntradayPrice(1), $latestIntradayPrice),
            'min5' => self::calculateChange($findIntradayPrice(5), $latestIntradayPrice),
            'min15' => self::calculateChange($findIntradayPrice(15), $latestIntradayPrice),
        ];

        $new_item['id'] = $highestId;
        $new_item['name'] = $symbol_name;
        $new_item['rank'] = 0;
        $new_item['symbol'] = $symbol;
        $new_item['image'] = 'https://ai-bubbles-web.appdevelop.in/api/v1/marketstack/image/'.$symbol;
        $new_item['price'] = $latestIntradayPrice;
        $new_item['volume'] = 0;
        $new_item['performance'] = $performance_intraday;
        $new_item['volumeWeekly'] = 0;




        $eodData = self::getEODData($symbol);

        $latestEODPrice = $eodData[0]['close'];
        $latestEODVolume = $eodData[0]['volume'];
        $reversed = array_reverse($eodData);
        $weekly = self::convertCandles($reversed, 'weekly');
        $monthly = self::convertCandles($reversed, 'monthly');
        $yearly = self::convertCandles($reversed, 'yearly');

        $findEODPrice = function ($daysAgo) use ($eodData) {
            return $eodData[$daysAgo]['close'] ?? null;
        };

        $performance_eod = [
            'day' => self::calculateChange($findEODPrice(1), $latestEODPrice),
            'week' => self::calculateChange($weekly[1]['close'], $weekly[0]['close']),
            'month' => self::calculateChange($monthly[1]['close'], $monthly[0]['close']),
            'year' => self::calculateChange($yearly[1]['close'], $yearly[0]['close']),
        ];

        $new_item['performance'] = array_merge($performance_intraday,$performance_eod);
        $new_item['price'] = $latestEODPrice;
        $new_item['volume'] = $latestEODVolume;
        $new_item['volumeWeekly'] = $weekly[0]['volume'];

        return $new_item;
    }
}
