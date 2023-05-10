<?php

namespace App\Http\Resources;

use App\Models\ImportShipment;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ImportShipmentResource extends JsonResource
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
            'data' => $this->resource->map(fn (ImportShipment $importShipment) => [
                'id' => $importShipment->id,
                'import_date' => $importShipment->import_date,
                'description' => $importShipment->description,
                'supplier_id' => $importShipment->supplier_id,
                'supplier_name' => $importShipment->supplier->name,
                'quantity' => $importShipment->quantity,
                'import_code' => $importShipment->import_code,
                'import_price_totail' => $importShipment->import_price_totail,
                'status' => $importShipment->status,
                'import_type' => $importShipment->import_type,
                'payment' => $importShipment->payment,
                'created_at' => $importShipment->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $importShipment->created_at)->format('d/m/Y'):'',
                'updated_at' => $importShipment->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $importShipment->updated_at)->format('d/m/Y'):''
            ])
        ];
    }
}
