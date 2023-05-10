<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExportShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'export_code',
        'quantity',
        'receve_phone',
        'export_date',
        'export_type',
        'payment',
        'supplier_id',
        'totall_price',
        'description'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exportShipmentDetails(): HasMany
    {
        return $this->hasMany(ExportShipmentDetail::class);
    }
}
