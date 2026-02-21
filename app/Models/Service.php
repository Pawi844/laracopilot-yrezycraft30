<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'short_description',
        'icon', 'category', 'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}