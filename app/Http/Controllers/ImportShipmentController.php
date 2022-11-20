<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportShipmentRepuest;
use App\Models\ImportShipment;
use App\Models\ImportShipmentDetail;
use App\Models\Product;
use App\Models\ProductVersion;

class ImportShipmentController extends Controller
{
    public function save(ImportShipmentRepuest $request)
    {
        $createImportShipmentData = $request->all();
        $products = collect($createImportShipmentData['products']);
        $createImportShipmentData['import_code'] = $this->GetImportCode();
        $createImportShipmentData['quantity'] = array_sum($products->pluck('quantity')->toArray());
        $createImportShipmentData['import_price_totail'] = array_sum($products->pluck('import_price')->toArray());
        if ($importShipment = ImportShipment::query()->create($createImportShipmentData)) {

            $importShipmentDetailDatas = $this->getImportShipmentDetailData($importShipment->id, $request->products);
            foreach ($importShipmentDetailDatas as $importShipmentDetailData) {
                $importShipmentDetail = ImportShipmentDetail::query()->create($importShipmentDetailData);

                $product = Product::find($importShipmentDetail->product_id);
                if ($product->import_price !== $importShipmentDetail->import_price) {
                    $product->quantity += $request->quantity;
                    $product->save();
                }

                $product->quantity += $request->quantity;
                $product->import_price = $request->import_price;

                $productVersionData = [
                    'name' => $product->name,
                    'import_price' => $product->import_price,
                    'price' => $product->price,
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'category_id' => $product->category_id
                ];

                ProductVersion::query()->create($productVersionData);
                $product->save();
            }
        }
    }

    protected function GetImportCode()
    {
        $latestImportShipment = ImportShipment::latest('id')->first(['id']);
        $latestId = $latestImportShipment->id ?? 0;

        return 'MNH' . str_pad(++$latestId, 7, '0', STR_PAD_LEFT);
    }

    protected function getImportShipmentDetailData($importShipmentId, $products)
    {
        $result = [];

        foreach ($products as $product) {
            $item['import_shipment_id'] = $importShipmentId;
            $item['product_id'] = $product['id'];
            $item['quantity'] = $product['quantity'];
            $item['import_price'] = $product['import_price'];
            $result[] = $item;
        }

        return $result;
    }
}
