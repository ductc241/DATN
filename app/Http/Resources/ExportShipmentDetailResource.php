<?php

namespace App\Http\Resources;

use App\Models\ExportShipmentDetail;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ExportShipmentDetailResource extends JsonResource
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
            'data' => $this->resource->map(fn (ExportShipmentDetail $exportShipmentDetail) => [
                'id' => $exportShipmentDetail->id,
                'product_id' => $exportShipmentDetail->product_id,
                'product_name' => $exportShipmentDetail->product->name,
                'export_shipment_id' => $exportShipmentDetail->export_shipment_id,
                'quantity' => $exportShipmentDetail->quantity,
                'price' => $exportShipmentDetail->price,
                'lot_code' => $exportShipmentDetail->lot_code,
                'customer' => $exportShipmentDetail->ExportShipment->supplier,
                'created_at' => $exportShipmentDetail->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $exportShipmentDetail->created_at)->format('d/m/Y') : '',
                'updated_at' => $exportShipmentDetail->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $exportShipmentDetail->updated_at)->format('d/m/Y') : ''
            ])
        ];
    }
}
