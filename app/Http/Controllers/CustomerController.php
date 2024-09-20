<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-index|customer-create|customer-delete', ['only' => ['index']]);
        $this->middleware('permission:customer-create', ['only' => ['create','store', 'updateStatus']]);
        $this->middleware('permission:customer-delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {
        $status = $request->has('status') && $request->status !== '' ? $request->status : null;
        if($status!=null)
        {
            $status = $status==1?true:false;
        }

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : null;

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
    
        $customers = Customer::when(!is_null($status), function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when($dateFrom, function ($query) use ($dateFrom) {
            return $query->where('created_at', '>=', $dateFrom);
        })
        ->when($dateTo, function ($query) use ($dateTo) {
            return $query->where('created_at', '<=', $dateTo);
        })
        ->orderBy($sortBy, $sortOrder)
        ->paginate(10);
    
        return view('customer.index', [
            'customers' => $customers,
            'status' => $status,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {

            $customer = Customer::findOrfail($request->customer_id);
            Customer::whereId($customer->id)->delete();

            DB::commit();
            return redirect()->route('users.index')->with('success', 'Customer Deleted Successfully!.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }


    public function updateStatus($customer_id, $status)
    {
        $validate = Validator::make([
            'customer_id'   => $customer_id,
            'status'    => $status
        ], [
            'customer_id'   =>  'required|exists:customers,id',
            'status'    =>  'required|in:0,1',
        ]);


        if($validate->fails()){
            return redirect()->route('customer.index')->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();


            Customer::whereId($customer_id)->update(['status' => $status]);


            DB::commit();
            return redirect()->route('customer.index')->with('success','Customer Status Updated Successfully!');
        } catch (\Throwable $th) {


            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
