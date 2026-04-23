<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'flat_id',
        'tenant_id',
        'monthly_rental_amount',
        'service_charge',
        'date',
        'remark',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function flat()
    {
        return $this->belongsTo(OwnerFlat::class, 'flat_id');
    }


    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }



}
