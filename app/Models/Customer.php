<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number',
        'address',
        'opening_balance',
    ];

    protected $appends = ['closing_balance'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getClosingBalanceAttribute()
    {
        $opening = (float) $this->opening_balance;
        $invoiced = (float) $this->invoices()->sum('amount');
        $paid = (float) $this->receipts()->sum('amount');

        return $opening + $invoiced - $paid;
    }
}
