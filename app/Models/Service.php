<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'amount', 'marla', 'details', 'is_category'];
    
    protected $casts = [
        'details' => 'array',
        'is_category' => 'boolean',
    ];

    public function scopeCategories($query)
    {
        return $query->where('is_category', true);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_service')->withPivot('amount')->withTimestamps();
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_service')->withPivot('amount', 'marla')->withTimestamps();
    }
}
