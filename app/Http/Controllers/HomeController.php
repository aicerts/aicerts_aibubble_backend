<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $jsondata = json_decode(file_get_contents(public_path('stocks/marketstack.json')), true);
        
        $updatedSymbols = [];
        foreach ($jsondata as $data) {
            $imageFolder = public_path('stocks/assets/');
            $stock = $data['symbol'];
            $files = collect(File::files($imageFolder))->filter(function ($file) use ($stock) {
                return preg_match("/{$stock}\./", $file->getFilename());
            });

            if ($files->isNotEmpty()) {
                $data['image'] = asset('/stocks/assets/' . $files->first()->getRelativePathname());
            }
            $updatedSymbols[] = $data;
        }


        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 10;


        $currentItems = array_slice($updatedSymbols, ($currentPage - 1) * $perPage, $perPage);

        $paginatedItems = new LengthAwarePaginator($currentItems, count($updatedSymbols), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);


        return view('home', compact('paginatedItems'));
    }

    /**
     * User Profile
     * @param Nill
     * @return View Profile
     * @author 
     */
    public function getProfile()
    {
        return view('profile');
    }

    /**
     * Update Profile
     * @param $profileData
     * @return Boolean With Success Message
     * @author 
     */
    public function updateProfile(Request $request)
    {

        $user_id = auth()->user()->id;
        #Validations
        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|unique:users,email,'.$user_id.',id',
            'mobile_number' => 'required|numeric|digits:10',
        ]);

        try {
            DB::beginTransaction();
            
            #Update Profile Data
            User::whereId(auth()->user()->id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            #Commit Transaction
            DB::commit();

            #Return To Profile page with success
            return back()->with('success', 'Profile Updated Successfully.');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Change Password
     * @param Old Password, New Password, Confirm New Password
     * @return Boolean With Success Message
     * @author 
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        try {
            DB::beginTransaction();

            #Update Password
            User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
            
            #Commit Transaction
            DB::commit();

            #Return To Profile page with success
            return back()->with('success', 'Password Changed Successfully.');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
