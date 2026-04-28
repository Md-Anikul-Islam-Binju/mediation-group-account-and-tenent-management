<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'rent_id',
        'owner_id',
        'owner_name',
        'flat_id',
        'flat_address',
        'tenant_id',
        'tenant_name',
        'monthly_rental_amount',
        'service_charge',
        'date',
        'month',
        'is_extra_amount',
        'total_amount',
        'due_amount',
        'paid_amount',
        'remark',
        'status',
    ];
}
