<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'contact_number', 'delivery_address'];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
