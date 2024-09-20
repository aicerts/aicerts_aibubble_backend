<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function settings()
    {
        $settings = [];
        $settings['terms_contions'] = BusinessSetting::where('key','terms_conditions')->first()->value??"";
        $settings['privacy_policy'] = BusinessSetting::where('key','privacy_policy')->first()->value??"";

        return response()->json(['status' => true,'data' => $settings], 200);
    }
}
