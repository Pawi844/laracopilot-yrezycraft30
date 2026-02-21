<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'name', 'email', 'phone',
        'subject', 'message', 'status', 'priority', 'admin_notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}