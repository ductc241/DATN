<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RefundExportShipmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_export_shipment_id',
        'product_id',
        'quantity',
        'export_price',
        'refund_price',
        'lot_code',
        'status'
    ];

    public function refundExportShipment(): HasOne
    {
        return $this->hasOne(RefundExportShipment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
