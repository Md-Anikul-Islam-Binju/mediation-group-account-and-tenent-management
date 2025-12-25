<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\OwnerBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class OwnerBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (!Gate::allows('owner-bank-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $owners = Owner::latest()->get();
        $ownerBank = OwnerBank::latest()->get();
        return view('admin.pages.ownerBank.index', compact('owners', 'ownerBank'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'owner_id'  => 'required|exists:owners,id',
                'bank_name'  => 'required|string|max:255',
                'account_holder_name'  => 'required|string|max:255',
                'branch_name'  => 'required|string|max:255',
                'account_number'  => 'required|string|max:255',
                'route_number'  => 'nullable|string|max:255',
                'remark'  => 'nullable|string',
            ]);


            OwnerBank::create([
                'owner_id'            => $request->owner_id,
                'bank_name'           => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'branch_name'         => $request->branch_name,
                'account_number'      => $request->account_number,
                'route_number'        => $request->route_number,
                'remark'              => $request->remark,
            ]);

            Toastr::success('Owner Bank Account Added Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ownerBank = OwnerBank::findOrFail($id);

            $request->validate([
                'owner_id'  => 'required|exists:owners,id',
                'bank_name'  => 'required|string|max:255',
                'account_holder_name'  => 'required|string|max:255',
                'branch_name'  => 'required|string|max:255',
                'account_number'  => 'required|string|max:255',
                'route_number'  => 'nullable|string|max:255',
                'remark'  => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);

            $ownerBank->update([
                'owner_id'            => $request->owner_id,
                'bank_name'           => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'branch_name'         => $request->branch_name,
                'account_number'      => $request->account_number,
                'route_number'        => $request->route_number,
                'remark'              => $request->remark,
                'status'              => $request->status,
            ]);

            Toastr::success('Owner Bank Account Updated Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $ownerBank = OwnerBank::findOrFail($id);
            $ownerBank->delete();
            Toastr::success('Owner Bank Account Deleted Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
