<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\OwnerFlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class OwnerFlatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            abort_if(!Gate::allows('owner-flat-list'), 403);
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $owners = Owner::latest()->get();
        $ownerFlats = OwnerFlat::with('owner')->latest()->get();

        return view('admin.pages.ownerFlat.index', compact('owners', 'ownerFlats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'address' => 'required',
            'monthly_rental_amount' => 'required|numeric',
            'service_charge' => 'nullable|numeric',
            'security_deposit_month' => 'nullable|integer',
            'security_deposit_amount' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        OwnerFlat::create([
            'owner_id' => $request->owner_id,
            'flat_uniq_code' => $this->generateFlatCode(),
            'address' => $request->address,
            'monthly_rental_amount' => $request->monthly_rental_amount,
            'service_charge' => $request->service_charge,
            'security_deposit_month' => $request->security_deposit_month,
            'security_deposit_amount' => $request->security_deposit_amount,
            'remark' => $request->remark,
            'status' => 'Vacant',
        ]);

        Toastr::success('Owner Flat Added Successfully');
        return redirect()->back();
    }


    public function update(Request $request, $id)
    {
        $flat = OwnerFlat::findOrFail($id);

        $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'address' => 'required',
            'monthly_rental_amount' => 'required|numeric',
            'service_charge' => 'nullable|numeric',
            'security_deposit_month' => 'nullable|integer',
            'security_deposit_amount' => 'nullable|numeric',
            'remark' => 'nullable|string',
            'status' => 'required|in:Vacant,Booked',
        ]);

        $flat->update([
            'owner_id' => $request->owner_id,
            'address' => $request->address,
            'monthly_rental_amount' => $request->monthly_rental_amount,
            'service_charge' => $request->service_charge,
            'security_deposit_month' => $request->security_deposit_month,
            'security_deposit_amount' => $request->security_deposit_amount,
            'remark' => $request->remark,
            'status' => $request->status,
        ]);

        Toastr::success('Owner Flat Updated Successfully');
        return redirect()->back();
    }


    public function destroy($id)
    {
        $ownerFlat = OwnerFlat::findOrFail($id);
        $ownerFlat->delete();

        Toastr::success('Owner Flat Deleted Successfully', 'Success');
        return redirect()->back();
    }

    private function generateFlatCode()
    {
        return 'FC-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
    }

}
