<?php

namespace App\Http\Controllers\Api\Stocks;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

class StocksController extends Controller
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

    public function stocks_data()
    {
        $updatedJson = [];
        foreach($this->jsonData as $data)
        {
            $imageFolder = public_path('stocks/assets/');
            $stock = $data['symbol'];
            $files = collect(File::files($imageFolder))->filter(function ($file) use ($stock) {
                return preg_match("/{$stock}\./", $file->getFilename());
            });

            if ($files->isNotEmpty()) {
                $data['image'] = asset('/stocks/assets/'.$files->first()->getRelativePathname());
            }
            $updatedJson[] = $data;
        }

        return $updatedJson;
    }

    public function stocks_chart($symbol, $interval)
    {
        $data = [];

        switch ($interval) {
            case 'hour':
                $data = Helpers::getIntradayData($symbol);
                break;
            case 'day':
                $data = Helpers::getIntradayData($symbol, '10min', 144);
                break;
            case 'week':
                $data = Helpers::getIntradayData($symbol, '1hour', 167);
                break;
            case 'month':
                $data = Helpers::getIntradayData($symbol, '6hour', 121);
                break;
            case 'year':
                $data = Helpers::getEODData($symbol);
                break;
        }

        $data = collect($data)->map(function ($item) use ($interval) {
            return [
                'p' => $interval === 'year' ? $item['close'] : $item['last'],
                't' => strtotime($item['date']),
                'date' => $item['date'],
            ];
        })->filter(function ($item) {
            return !is_null($item['p']);
        })->reverse()->values()->all();

        return response()->json($data);
    }


}
