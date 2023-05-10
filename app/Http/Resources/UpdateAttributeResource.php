<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateAttributeResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d/m/Y'):'',
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('d/m/Y'):''
        ];
    }
}
