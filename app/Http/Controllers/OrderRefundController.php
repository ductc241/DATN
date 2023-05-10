<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRefundExportShipmentRequest;
use App\Http\Requests\OrderRefundRequest;
use App\Http\Requests\SearchRefundExportShipmentRequest;
use App\Http\Resources\GetRefundExportShipmentResource;
use App\Http\Resources\RefundExportShipmentResource;
use App\Models\ExportShipment;
use App\Models\Product;
use App\Models\productDetail;
use App\Models\RefundExportShipment;
use App\Models\RefundExportShipmentDetail;
use Illuminate\Database\Eloquent\Builder;

class OrderRefundController extends Controller
{

    public function index(SearchRefundExportShipmentRequest $request)
    {
        $refundExportShipment = RefundExportShipment::query()
            ->when($request['refund_code'], function (Builder $query, string $refundCode) {
                $query->where('refund_code', 'like', '%' . $refundCode . '%');
            })
            ->with('user', 'supplier', 'refundExportShipmentDetail', 'refundExportShipmentDetail.product', 'refundExportShipmentDetail.product.productDetails')->get();
        return new GetRefundExportShipmentResource($refundExportShipment);
    }

    public function show($id)
    {
        $refundExportShipment = RefundExportShipment::query()->with('refundExportShipmentDetail', 'refundExportShipmentDetail.product')->where('id', $id)->first();
        return $refundExportShipment;
    }

    public function store(CreateRefundExportShipmentRequest $request)
    {
        $refundExportShipmentData = $request->all();
        $createrefundExportShipmentData = [];
        $products = collect($refundExportShipmentData['products']);
        $createrefundExportShipmentData['refund_code'] = $this->GetRefundExportCode();
        $createrefundExportShipmentData['export_shipment_id'] = $refundExportShipmentData['export_shipment_id'];
        $createrefundExportShipmentData['refund_type'] = $refundExportShipmentData['refund_type'];
        $createrefundExportShipmentData['user_id'] = $refundExportShipmentData['user_id'];
        $createrefundExportShipmentData['supplier_id'] = $refundExportShipmentData['supplier_id'];
        $createrefundExportShipmentData['description'] = $refundExportShipmentData['description'];
        $createrefundExportShipmentData['refund_totall_quantity'] = array_sum($products->pluck('quantity')->toArray());
        $createrefundExportShipmentData['refund_price_totail'] = array_sum($this->GetTotallPrice($products));

        if ($refundExportShipment = RefundExportShipment::query()->create($createrefundExportShipmentData)) {
            $refundExportShipmentDetailDatas = $this->getRefundExportShipmentDetailData($refundExportShipment->id, $request->products);

            foreach ($refundExportShipmentDetailDatas as $refundExportShipmentDetailData) {

                $refundExportShipmentDetail = RefundExportShipmentDetail::query()->create($refundExportShipmentDetailData);
                $productDetail = productDetail::query()->where('product_id', $refundExportShipmentDetail->product_id)->where('lot_code', $refundExportShipmentDetail->lot_code)->first();

                $productDetail->quantity += $refundExportShipmentDetail->quantity;
                $productDetail->save();

                $product = Product::find($refundExportShipmentDetail->product_id);
                $product->quantity += $refundExportShipmentDetail->quantity;
                $product->save();
            }
            return true;
        }
        return false;
    }

    protected function getRefundExportShipmentDetailData($refundExportShipmentId, $products)
    {
        $result = [];

        foreach ($products as $product) {
            $item['refund_export_shipment_id'] = $refundExportShipmentId;
            $item['product_id'] = $product['id'];
            $item['quantity'] = $product['quantity'];
            $item['export_price'] = $product['export_price'];
            $item['refund_price'] = $product['refund_price'];
            $item['lot_code'] = $product['lot_code'];
            $result[] = $item;
        }

        return $result;
    }

    protected function GetRefundExportCode()
    {
        $latestExportShipment = RefundExportShipment::latest('id')->first(['id']);
        $latestId = $latestExportShipment->id ?? 0;

        return 'HH' . str_pad(++$latestId, 9, '0', STR_PAD_LEFT);
    }

    protected function GetTotallPrice($products)
    {
        $result = [];

        foreach ($products as $product) {
            $result[] = $product['quantity'] * $product['refund_price'];
        }

        return $result;
    }

    public function SearchExportShipment(OrderRefundRequest $request)
    {
        $exportShipment = ExportShipment::query()
            ->when($request['export_code'], function (Builder $query, $exportCode) {
                $query->where('export_code', 'like', '%' . $exportCode . '%');
            })
            ->with('supplier','exportShipmentDetails','exportShipmentDetails.product')->get();
        return new RefundExportShipmentResource($exportShipment);
    }
}
