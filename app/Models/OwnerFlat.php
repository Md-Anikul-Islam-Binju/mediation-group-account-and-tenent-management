<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerFlat extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'flat_uniq_code',
        'address',
        'monthly_rental_amount',
        'service_charge',
        'security_deposit_month',
        'security_deposit_amount',
        'payment_mode',
        'remark',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

}
