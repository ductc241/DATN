<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportShipmentRequest;
use App\Http\Requests\SearchExportShipment;
use App\Http\Resources\ExportShipmentDetailResource;
use App\Http\Resources\ExportShipmentResource;
use App\Models\ExportShipment;
use App\Models\ExportShipmentDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ExportShipmentController extends Controller
{
    public function index(SearchExportShipment $request)
    {
        $importShipments = ExportShipment::query()
            ->when($request->keyword, function (Builder $query, string $keyword) {
                $query->where('export_code', 'like', '%' . $keyword . '%');
            })
            ->with('user')
            ->with('supplier')
            ->orderBy('created_at', 'DESC')->paginate(15);
        return new ExportShipmentResource($importShipments);
    }

    public function save(ExportShipmentRequest $request)
    {
        $ExportShipmentData = $request->all();
        $createExportShipmentData = [];
        if ($ExportShipmentData['export_type'] === 2) {
            $insertSupplierData = [
                'name' => $ExportShipmentData['user_name'],
                'phone_number' => $ExportShipmentData['phone_number'],
                'status' => 2
            ];
            $supplier = Supplier::query()->create($insertSupplierData);
            $createExportShipmentData['supplier_id'] = $supplier->id;
        } else {
            $createExportShipmentData['supplier_id'] = $ExportShipmentData['supplier_id'];
        }
        $products = collect($ExportShipmentData['products']);
        $createExportShipmentData['export_code'] = $this->GetExportCode();
        $createExportShipmentData['export_type'] = $ExportShipmentData['export_type'];
        $createExportShipmentData['payment'] = $ExportShipmentData['payment'];
        $createExportShipmentData['user_id'] = $ExportShipmentData['user_id'];
        $createExportShipmentData['description'] = $ExportShipmentData['description'];
        $createExportShipmentData['quantity'] = array_sum($products->pluck('quantity')->toArray());
        $createExportShipmentData['totall_price'] = array_sum($this->GetTotallPrice($products));
        $createExportShipmentData['export_date'] = Carbon::createFromFormat('d/m/Y', $ExportShipmentData['export_date'])->format('Y-m-d H:i:s');
        if ($exportShipment = ExportShipment::query()->create($createExportShipmentData)) {

            $exportShipmentDetailDatas = $this->getExportShipmentDetailData($exportShipment->id, $request->products);

            foreach ($exportShipmentDetailDatas as $exportShipmentDetailData) {

                $exportShipmentDetail = ExportShipmentDetail::query()->create($exportShipmentDetailData);
                $productDetail = ProductDetail::query()->where('product_id', $exportShipmentDetail->product_id)->where('lot_code', $exportShipmentDetail->lot_code)->first();
                $productDetail->quantity -= $exportShipmentDetail->quantity;
                $productDetail->save();

                $product = Product::find($exportShipmentDetail->product_id);
                $product->quantity -= $exportShipmentDetail->quantity;
                $product->save();
            }
        }
        return true;
    }

    protected function GetTotallPrice($products)
    {
        $result = [];

        foreach ($products as $product) {
            $result[] = $product['quantity'] * $product['price'];
        }

        return $result;
    }

    protected function GetExportCode()
    {
        $latestExportShipment = ExportShipment::latest('id')->first(['id']);
        $latestId = $latestExportShipment->id ?? 0;

        return 'PX' . str_pad(++$latestId, 9, '0', STR_PAD_LEFT);
    }

    protected function getExportShipmentDetailData($exportShipmentId, $products)
    {
        $result = [];

        foreach ($products as $product) {
            $item['export_shipment_id'] = $exportShipmentId;
            $item['product_id'] = $product['id'];
            $item['quantity'] = $product['quantity'];
            $item['price'] = $product['price'];
            $item['lot_code'] = $product['lot_code'];
            $result[] = $item;
        }

        return $result;
    }

    public function getDetail($export_id)
    {
        $exportShipmentDetail = ExportShipmentDetail::query()->where('export_shipment_id', $export_id)->with('product','ExportShipment.supplier')->get();
        return new ExportShipmentDetailResource($exportShipmentDetail);
    }
}
