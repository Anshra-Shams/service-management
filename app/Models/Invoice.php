<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'service_id', 'service_date', 'due_date', 'amount', 'category', 'type'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'invoice_service')
                    ->withPivot('amount', 'monthly_charges', 'arrears', 'late_surcharge', 'total_bills', 'total_payable')
                    ->withTimestamps();
    }
}
