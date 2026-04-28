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

}
