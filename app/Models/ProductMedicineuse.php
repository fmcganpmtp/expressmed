<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedicineuse extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id','medicine_for','medicine_use'
    ];
}
