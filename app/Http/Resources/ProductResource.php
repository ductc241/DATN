<?php

namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'data' => $this->resource
        ];
    }
}
