<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'amount', 'marla', 'details'];
    
    protected $casts = [
        'details' => 'array',
    ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_service')->withPivot('amount')->withTimestamps();
    }
}
