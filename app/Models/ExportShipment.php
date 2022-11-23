<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'totall_price',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
