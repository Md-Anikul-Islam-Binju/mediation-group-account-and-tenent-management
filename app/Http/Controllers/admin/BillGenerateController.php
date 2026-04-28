<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Rent;
use Illuminate\Http\Request;

class BillGenerateController extends Controller
{
    public function generate()
    {
        $month = now()->format('F-Y');

        // Check if already generated for this month
        $alreadyGenerated = Bill::where('month', $month)->exists();

        if ($alreadyGenerated) {
            return back()->with('error', 'Bill already generated for this month!');
        }



        $rents = Rent::with(['owner','flat','tenant'])->get();

        foreach ($rents as $rent) {

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

        return back()->with('success', 'Bills generated for ' . $month);
    }
}
