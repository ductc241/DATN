<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateProductResource extends JsonResource
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
            'name' => $this->name,
            'category_id' => $this->category_id,
            'sku' => $this->sku,
            'import_price' => $this->import_price,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'description' => $this->description,
            'status' => $this->status,
            'warranty_date' => $this->warranty_date,

            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d/m/Y') : '',
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('d/m/Y') : ''
        ];
    }
}
