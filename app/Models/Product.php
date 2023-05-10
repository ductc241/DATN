<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'price',
        'quantity',
        'description',
        'status',
        'warranty_date',
    ];
    
    public function Category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(ExportShipmentDetail::class);
    }

    public function productDetails()
    {
        return $this->hasMany(productDetail::class);
    }
}
