<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'invited_by',
        'email',
        'token',
        'invited_by'
    ];
}
