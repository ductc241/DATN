<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'import_shipment_detail_id',
        'quantity',
        'lot_code',
        'import_price'
    ];

}
