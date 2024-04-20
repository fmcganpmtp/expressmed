<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;
    protected $fillable=[
    'order_id','payment_gateway','transaction_id','amount','transaction_date','currency_code','payment_method','transaction_status'

    ];
}
