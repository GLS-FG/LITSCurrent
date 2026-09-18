<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCopy extends Model
{
    protected $fillable = ['order_shipment_id', 'order_id'];
}
