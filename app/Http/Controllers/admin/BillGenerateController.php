<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Rent;
use Illuminate\Http\Request;

class BillGenerateController extends Controller
{
    public function start()
    {
        $month = now()->format('Y-m');

        // already generated?
        $exists = Bill::where('month', $month)->exists();

        if ($exists) {
            return response()->json([
                'status' => 'exists'
            ]);
        }

        $total = Rent::count();

        return response()->json([
            'status' => 'ok',
            'total' => $total
        ]);
    }

    public function step(Request $request)
    {
        $offset = $request->offset ?? 0;
        $month = now()->format('Y-m');

        $rent = Rent::with(['owner','flat','tenant'])
            ->skip($offset)
            ->first();

        if (!$rent) {
            return response()->json([
                'done' => true
            ]);
        }

        // avoid duplicate
        $exists = Bill::where('rent_id', $rent->id)
            ->where('month', $month)
            ->exists();

        if (!$exists) {

            $total = ($rent->monthly_rental_amount ?? 0) + ($rent->service_charge ?? 0);

            Bill::create([
                'invoice_number' => 'INV-' . date('Ym') . '-' . $rent->id,

                'rent_id' => $rent->id,

                'owner_id' => $rent->owner_id,
                'owner_name' => $rent->owner->name ?? '',

                'flat_id' => $rent->flat_id,
                'flat_address' => $rent->flat->address ?? '',

                'tenant_id' => $rent->tenant_id,
                'tenant_name' => $rent->tenant->name ?? '',

                'monthly_rental_amount' => $rent->monthly_rental_amount,
                'service_charge' => $rent->service_charge,

                'date' => now(),
                'month' => $month,

                'total_amount' => $total,
                'due_amount' => $total,
                'paid_amount' => 0,

                'status' => 'pending',
            ]);
        }

        return response()->json([
            'done' => false,
            'next' => $offset + 1
        ]);
    }


    public function index(Request $request)
    {
        $query = Bill::query();

        // month filter
        $month = $request->month ?? now()->format('Y-m');
        $query->where('month', $month);

        // address filter
        if ($request->address) {
            $query->where('flat_address', 'like', '%' . $request->address . '%');
        }

        // PAGINATION (IMPORTANT)
        $bills = $query->latest()->paginate(100)->withQueryString();

        // COLLECTION BASED SUMMARY
        $totalAmount = $bills->sum('total_amount');
        $totalDue = $bills->sum('due_amount');
        $totalPaid = $bills->sum('paid_amount');

        return view('admin.pages.bill.index', compact(
            'bills',
            'month',
            'totalAmount',
            'totalDue',
            'totalPaid'
        ));
    }

    public function update(Request $request, $id)
    {

        $bill = Bill::findOrFail($id);

        $extra = [];

        if ($request->extra_key && $request->extra_value) {
            foreach ($request->extra_key as $i => $key) {

                if ($key !== null && $key !== '') {
                    $extra[$key] = $request->extra_value[$i] ?? 0;
                }

            }
        }

        $bill->update([
            'status' => $request->status,
            'is_extra_amount' => json_encode($extra),
        ]);

        return back()->with('success', 'Updated successfully');
    }

}
