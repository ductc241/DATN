<?php

namespace App\Http\Resources;

use App\Models\productDetail;
use Illuminate\Http\Resources\Json\JsonResource;

class productDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data'=>$this->resource->map(fn(productDetail $productDetail)=>[
                'id'=>$productDetail->id,
                'product_id'=>$productDetail->product_id,
                'quantity'=>$productDetail->quantity,
                'import_price'=>$productDetail->import_price,
                'lot_code'=>$productDetail->lot_code
            ])
        ];
    }
}
