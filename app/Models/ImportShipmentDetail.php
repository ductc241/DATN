<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportShipmentDetail extends Model
{
    use HasFactory;
    public $table = "import_shipment_detail";

    protected $fillable = [
        'product_id',
        'import_shipment_id',
        'quantity',
        'import_price',
        'status'
    ];

    public function product(){

        return $this->belongsTo(Product::class);
    }

    public function productDetails(){

        return $this->hasMany(productDetail::class,'import_shipment_detail_id','id');
    }
}
