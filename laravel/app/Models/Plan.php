<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'specs', 'is_custom', 'deposit_per_unit', 'daily_rate', 'weekly_rate', 'monthly_rate', 'active'];

    protected $casts = ['is_custom' => 'boolean', 'active' => 'boolean'];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
