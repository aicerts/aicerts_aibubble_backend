<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:settings-index', ['only' => ['terms_contions_view','privacy_policy_view']]);
        $this->middleware('permission:settings-update', ['only' => ['terms_conditions_store','privacy_policy_store']]);
    }
    
    public function terms_contions_view()
    {
        $terms = BusinessSetting::where('key','terms_conditions')->first()->value??"";
        return view('business_setting.terms_conditions',compact('terms'));
    }

    public function terms_conditions_store(Request $request)
    {
        if($request->terms_conditions)
        {        
            $termsSetting = BusinessSetting::updateOrCreate(
                ['key' => 'terms_conditions'],
                ['value' => $request->input('terms_conditions')]
            );
        }

        return redirect()->back()->with('success', 'Terms & Conditions updated successfully.');
    }

    public function privacy_policy_view()
    {
        $privacy_policy = BusinessSetting::where('key','privacy_policy')->first()->value??"";
        return view('business_setting.privacy_policy',compact('privacy_policy'));
    }

    public function privacy_policy_store(Request $request)
    {
        if($request->privacy_policy)
        {        
            $termsSetting = BusinessSetting::updateOrCreate(
                ['key' => 'privacy_policy'],
                ['value' => $request->input('privacy_policy')]
            );
        }

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }
}
