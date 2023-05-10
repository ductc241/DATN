<?php

namespace App\Http\Resources;

use App\Models\ExportShipment;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ExportShipmentResource extends JsonResource
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
            'data' => $this->resource->map(fn (ExportShipment $exportShipment) => [
                'id' => $exportShipment->id,
                'description' => $exportShipment->description,
                'user_id' => $exportShipment->user_id,
                'user_name' => $exportShipment->user->name,
                'seller_name' => $exportShipment->supplier->name,
                'address' => $exportShipment->address,
                'export_code' => $exportShipment->export_code,
                'receve_phone' => $exportShipment->receve_phone,
                'totall_price' => $exportShipment->totall_price,
                'quantity' => $exportShipment->quantity,
                'export_date' => $exportShipment->export_date,
                'status' => $exportShipment->status,
                'export_type' => $exportShipment->export_type,
                'payment' => $exportShipment->payment,
                'created_at' => $exportShipment->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $exportShipment->created_at)->format('d/m/Y') : '',
                'updated_at' => $exportShipment->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $exportShipment->updated_at)->format('d/m/Y') : ''
            ])
        ];
    }
}
