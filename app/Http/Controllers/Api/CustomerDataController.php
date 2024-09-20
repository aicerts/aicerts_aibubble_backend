<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDataController extends Controller
{
    public function set_data(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if($request->all())
        {
            UserData::updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'data' => $request->all()
                ]
            );
        }

        return response()->json(['status' => true, 'message' => 'Data saved successfully'], 200);
    }

    public function get_data(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $data = UserData::where('user_id',$customer->id)->first();
        return response()->json(['status' => true,'data' => $data->data??null], 200);
    }
}
