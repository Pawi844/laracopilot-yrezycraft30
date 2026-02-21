<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NetworkZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'county', 'area', 'coverage_type', 'status', 'signal_strength', 'notes'
    ];

    protected $casts = [
        'signal_strength' => 'integer'
    ];
}