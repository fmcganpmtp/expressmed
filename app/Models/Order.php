<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ORDERED = 'ordered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';

    protected $fillable = [
        'user_id','device_id','address_id','date','status','total_amount','total_tax_amount','shipping_charge','grand_total','prescription_ids','shipment_id','payment_method','delivery_type','store_id'
    ];

    public function get_paroductsname($order_id)
    {
        $product_names = OrderDetails::select('products.product_name')
        ->join('products','products.id','order_details.product_id')
        ->where('order_id', $order_id)->get();



        return $product_names;
    }


}
