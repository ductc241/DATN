<?php

namespace App\Http\Resources;
use Carbon\Carbon; 
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateAttributeProductResource extends JsonResource
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
            'id' => $this->id, 
            'product_id' => $this->product_id,
            'attribute_id' => $this->attribute_id,
            'price' => $this->price
        ];
    }
}
