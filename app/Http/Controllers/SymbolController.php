<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Symbol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SymbolController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:symbol-index|symbol-create|symbol-delete|symbol-delete', ['only' => ['index']]);
        $this->middleware('permission:symbol-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:symbol-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:symbol-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {
        $symbols = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);
        $updatedSymbols = [];
        foreach ($symbols as $data) {
            $imageFolder = public_path('stocks/assets/');
            $stock = $data['symbol'];
            $files = collect(File::files($imageFolder))->filter(function ($file) use ($stock) {
                return preg_match("/{$stock}\./", $file->getFilename());
            });

            if ($files->isNotEmpty()) {
                $data['image'] = asset('stocks/assets/' . $files->first()->getRelativePathname());
            }
            $updatedSymbols[] = $data;
        }
        $symbolsCollection = collect($updatedSymbols);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentPageItems = $symbolsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedSymbols = new LengthAwarePaginator(
            $currentPageItems,
            $symbolsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('symbol.index', compact('paginatedSymbols'));
    }

    public function create(Request $request)
    {
        return view('symbol.create');
    }

    public function store(Request $request)
    {

        return $request->all();

        $request->validate([
            'symbol' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'icon' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $symbol_found = false;

        $stocks = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);

        foreach ($stocks as $stock) {
            if ($stock['symbol'] == $request->symbol) {
                $symbol_found = true;
                break;
            }
        }

        if ($symbol_found == true) {
            return redirect()->route('symbol.index')->with('error', 'Stock Symbol Is Already Added!');
        }


        $new_item = Helpers::add_new_stock_symbol($request->symbol, $request->name);

        if ($new_item == []) {
            return redirect()->route('symbol.index')->with('error', 'Stock Symbol Data Not Found!');
        }

        $stocks[] = $new_item;


        usort($stocks, function ($a, $b) {
            return $b['volume'] <=> $a['volume'];
        });

        foreach ($stocks as $index => &$item) {
            $item['rank'] = $index + 1;
        }


        $path = public_path('stocks/marketstack.json');
        File::put($path, json_encode($stocks));


        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imageName = $request->symbol . ".png";
            $image->move(public_path('stocks/assets'), $imageName);
        }

        return redirect()->route('symbol.index')->with('success', 'Stock Symbol created successfully!');
    }


    public function destroy(Request $request)
    {
        $symbol = $request->symbol_id;
        $stocks = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);

        $found = false;
        foreach ($stocks as $key => $stock) {
            if ($stock['symbol'] == $symbol) {
                unset($stocks[$key]);
                $found = true;
            }
        }

        if ($found == false) {
            return redirect()->route('symbol.index')->with('error', 'Stock Symbol Not Found!');
        }


        $stocks = array_values($stocks);

        usort($stocks, function ($a, $b) {
            return $b['volume'] <=> $a['volume'];
        });

        foreach ($stocks as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        $path = public_path('stocks/marketstack.json');
        File::put($path, json_encode($stocks));

        return redirect()->route('symbol.index')->with('success', 'Stock Symbol deleted successfully!');
    }

    public function search_symbol(Request $request)

    {
        
        $accessKey = 'b242b15c327b3332565729379530467d';
        $exchange = 'IEXG';
        $searchQuery = $request->input('search');
        $limit = $request->limit?$request->limit:100;
        $offset = $request->offset?$request->offset:0;

        $url = "http://api.marketstack.com/v1/tickers?access_key=$accessKey&exchange=$exchange&search=$searchQuery&limit=$limit&offset=$offset";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
    }
}
