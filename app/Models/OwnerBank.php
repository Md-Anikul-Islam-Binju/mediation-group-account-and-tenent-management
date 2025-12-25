<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'bank_name',
        'account_holder_name',
        'branch_name',
        'account_number',
        'route_number',
        'remark',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
