<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_no', 'customer_id', 'customer_name', 'email', 'contact_number', 'delivery_address',
        'plan_id', 'plan_name', 'plan_specs', 'is_custom_plan', 'quantity',
        'start_date', 'end_date', 'total_days',
        'delivery_option', 'delivery_fee',
        'rate_per_day', 'rental_fee', 'deposit_option', 'deposit_amount',
        'tax_percent', 'tax_amount', 'subtotal', 'total_payable',
        'agent_name', 'agent_contact', 'agent_email',
        'quotation_link', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_custom_plan' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public static function generateNumber(): string
    {
        $code   = strtoupper(substr(env('APP_MACHINE_CODE', 'MV'), 0, 2));
        $yymm   = now()->format('ym');          // e.g. 2601
        $prefix = "SR{$code}{$yymm}";           // e.g. SRMV2601
        $count  = static::where('quotation_no', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($count, 2, '0', STR_PAD_LEFT);  // e.g. SRMV260103
    }
}
