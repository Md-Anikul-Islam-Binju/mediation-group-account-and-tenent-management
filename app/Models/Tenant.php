<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'agreement_paper',
        'organization',
        'account_mode',
        'status',
    ];

    protected $casts = [
        'agreement_paper' => 'array',
    ];


}
