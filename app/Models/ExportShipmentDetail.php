<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportShipmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_shipment_id',
        'product_id',
        'quantity',
        'price',
        'lot_code',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function product_detail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    public function ExportShipment()
    {
        return $this->belongsTo(ExportShipment::class);
    }
}
