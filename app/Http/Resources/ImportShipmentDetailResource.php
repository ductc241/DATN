<?php

namespace App\Http\Resources;

use App\Models\ImportShipmentDetail;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ImportShipmentDetailResource extends JsonResource
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
            'data' => $this->resource->map(fn (ImportShipmentDetail $importShipmentDetail) => [
                'product_id' => $importShipmentDetail->product_id,
                'product_name' => $importShipmentDetail->product->name,
                'import_shipment_id' => $importShipmentDetail->import_shipment_id,
                'quantity' => $importShipmentDetail->quantity,
                'lot_code' => $importShipmentDetail->lot_code,
                'import_price' => $importShipmentDetail->import_price,
                'status' => $importShipmentDetail->status,
                'created_at' => $importShipmentDetail->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $importShipmentDetail->created_at)->format('d/m/Y') : '',
                'updated_at' => $importShipmentDetail->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $importShipmentDetail->updated_at)->format('d/m/Y') : ''
            ])
        ];
    }
}
