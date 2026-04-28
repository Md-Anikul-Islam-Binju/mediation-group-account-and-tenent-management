<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Owner;
use App\Models\OwnerFlat;
use App\Models\Rent;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class RentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            abort_if(!Gate::allows('rent-list'), 403);
            return $next($request);
        })->only('index');
    }

    // INDEX (CLEAN)
    public function index()
    {
        $rents = Rent::with(['owner', 'flat', 'tenant'])->latest()->get();
        $owners = Owner::latest()->get();
        $tenants = Tenant::latest()->get();
        $flats = OwnerFlat::latest()->get();

        $currentMonth = now()->format('F-Y');
        $isGenerated = Bill::where('month', $currentMonth)->exists();
        return view('admin.pages.rent.index', compact('rents', 'owners','flats', 'tenants','isGenerated', 'currentMonth'));
    }

    // STORE (AUTO BOOK FLAT)
    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'flat_id' => 'required|exists:owner_flats,id',
            'tenant_id' => 'required|exists:tenants,id',
            'monthly_rental_amount' => 'required|numeric',
            'service_charge' => 'nullable|numeric',
            'date' => 'required|date',
            'remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            Rent::create([
                'owner_id' => $request->owner_id,
                'flat_id' => $request->flat_id,
                'tenant_id' => $request->tenant_id,
                'monthly_rental_amount' => $request->monthly_rental_amount,
                'service_charge' => $request->service_charge,
                'date' => $request->date,
                'remark' => $request->remark,
            ]);

            OwnerFlat::where('id', $request->flat_id)
                ->update(['status' => 'Booked']);
        });

        Toastr::success('Rent Created & Flat Booked', 'Success');
        return redirect()->back();
    }

    // UPDATE (SMART FLAT SWITCH)
    public function update(Request $request, $id)
    {
        $rent = Rent::findOrFail($id);

        $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'flat_id' => 'required|exists:owner_flats,id',
            'tenant_id' => 'required|exists:tenants,id',
            'monthly_rental_amount' => 'required|numeric',
            'service_charge' => 'nullable|numeric',
            'date' => 'required|date',
            'remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $rent) {

            $oldFlat = $rent->flat_id;

            $rent->update([
                'owner_id' => $request->owner_id,
                'flat_id' => $request->flat_id,
                'tenant_id' => $request->tenant_id,
                'monthly_rental_amount' => $request->monthly_rental_amount,
                'service_charge' => $request->service_charge,
                'date' => $request->date,
                'status' => $request->status,
                'remark' => $request->remark,
            ]);

            // old flat → Vacant
            OwnerFlat::where('id', $oldFlat)
                ->update(['status' => 'Vacant']);

            // new flat → Booked
            OwnerFlat::where('id', $request->flat_id)
                ->update(['status' => 'Booked']);
        });

        Toastr::success('Rent Updated Successfully', 'Success');
        return redirect()->back();
    }

    // DELETE (AUTO VACANT)
    public function destroy($id)
    {
        $rent = Rent::findOrFail($id);

        OwnerFlat::where('id', $rent->flat_id)
            ->update(['status' => 'Vacant']);

        $rent->delete();

        Toastr::success('Rent Deleted & Flat Vacant', 'Success');
        return redirect()->back();
    }

    // AJAX FLATS (ONLY VACANT)
    public function getOwnerFlats($owner_id)
    {
        return response()->json(
            OwnerFlat::where('owner_id', $owner_id)
                ->where('status', 'Vacant')
                ->get()
        );
    }
}
