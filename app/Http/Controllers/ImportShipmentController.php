<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportShipmentRepuest;
use App\Http\Requests\SearchImportShipment;
use App\Http\Resources\ImportShipmentDetailResource;
use App\Http\Resources\ImportShipmentResource;
use App\Models\ImportShipment;
use App\Models\ImportShipmentDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ImportShipmentController extends Controller
{
    public function index(SearchImportShipment $request)
    {
        $importShipments = ImportShipment::query()
            ->when($request->keyword, function (Builder $query, string $keyword) {
                $query->where('import_code', 'like', '%' . $keyword . '%');
            })
            ->with('supplier')
            ->orderBy('created_at', 'DESC')->paginate(15);
        return new ImportShipmentResource($importShipments);
    }

    public function save(ImportShipmentRepuest $request)
    {
        $ImportShipmentData = $request->all();
        $createImportShipmentData = [];
        if ($ImportShipmentData['import_type'] === 2) {
            $insertSupplierData = [
                'name' => $ImportShipmentData['user_name'],
                'phone_number' => $ImportShipmentData['phone_number']
            ];
            $supplier = Supplier::query()->create($insertSupplierData);
            $createImportShipmentData['supplier_id'] = $supplier->id;
        } else {
            $createImportShipmentData['supplier_id'] = $ImportShipmentData['supplier_id'];
        }
        $products = collect($ImportShipmentData['products']);
        $createImportShipmentData['import_date'] = Carbon::createFromFormat('d/m/Y', $ImportShipmentData['import_date'])->format('Y-m-d H:i:s');
        $createImportShipmentData['import_type'] = $ImportShipmentData['import_type'];
        $createImportShipmentData['user_id'] = $ImportShipmentData['user_id'];
        $createImportShipmentData['payment'] = $ImportShipmentData['payment'];
        $createImportShipmentData['description'] = $ImportShipmentData['description'];
        $createImportShipmentData['import_code'] = $this->GetImportCode();
        $createImportShipmentData['quantity'] = array_sum($products->pluck('quantity')->toArray());
        $createImportShipmentData['import_price_totail'] = array_sum($this->GetTotallPrice($products));
        if ($importShipment = ImportShipment::query()->create($createImportShipmentData)) {

            $importShipmentDetailDatas = $this->getImportShipmentDetailData($importShipment->id, $request->products);
            foreach ($importShipmentDetailDatas as $importShipmentDetailData) {

                $importShipmentDetail = ImportShipmentDetail::query()->create($importShipmentDetailData);
                $product = Product::find($importShipmentDetail->product_id);
                $createProductDetailData = [
                    'product_id' => $product->id,
                    'import_shipment_detail_id' => $importShipmentDetail->id,
                    'quantity' => $importShipmentDetail->quantity,
                    'import_price' => $importShipmentDetail->import_price,
                    'lot_code' => $importShipmentDetail->lot_code,
                ];
                ProductDetail::query()->create($createProductDetailData);

                $product->quantity += $importShipmentDetail->quantity;
                $product->save();
            }
        }

        return true;
    }

    protected function GetImportCode()
    {
        $latestImportShipment = ImportShipment::latest('id')->first(['id']);
        $latestId = $latestImportShipment->id ?? 0;

        return 'PN' . ++$latestId;
    }

    protected function getImportShipmentDetailData($importShipmentId, $products)
    {
        $result = [];

        foreach ($products as $product) {
            $item['import_shipment_id'] = $importShipmentId;
            $item['product_id'] = $product['id'];
            $item['quantity'] = $product['quantity'];
            $item['import_price'] = $product['import_price'];
            $item['lot_code'] = $this->getLotCode($importShipmentId);
            $result[] = $item;
        }

        return $result;
    }

    protected function GetIdSupplierByKeyword($keyword)
    {
        return Supplier::query()->where('name', 'iLIKE', '%' . $keyword . '%');
    }

    public function getDetail($import_id)
    {
        $importShipmentDetail = ImportShipmentDetail::query()->where('import_shipment_id', $import_id)->with('product')->get();
        return new ImportShipmentDetailResource($importShipmentDetail);
    }

    protected function GetTotallPrice($products)
    {
        $result = [];

        foreach ($products as $product) {
            $result[] = $product['quantity'] * $product['import_price'];
        }

        return $result;
    }
    

    protected function getLotCode($importShipmentId)
    {
        $result = 'ML' . $importShipmentId . '-' . date("Y-m-d");
        return $result;
    }
}
