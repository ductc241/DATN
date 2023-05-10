<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\productDetail;
use App\Models\RefundExportShipment;
use App\Models\RefundExportShipmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RefundOrderSupplierController extends Controller
{
    public function index()
    {
        return RefundExportShipment::query()->get();
    }

    public function save(Request $request)
    {
        $refundId = $request->id;
        $refund = RefundExportShipment::query()->with('refundExportShipmentDetail')->find($refundId);

        if ((int)$refund->refund_totall_quantity - (int)$request->refund_totall_quantity === 0) {
            $refund->update(['status', 2]);

            return true;
        }
        $createrefundExportShipmentData = [];
        $createrefundExportShipmentData['refund_code'] = $this->GetRefundExportCode();
        $createrefundExportShipmentData['export_shipment_id'] = $refund['export_shipment_id'];
        $createrefundExportShipmentData['refund_type'] = $refund['refund_type'];
        $createrefundExportShipmentData['user_id'] = $refund['user_id'];
        $createrefundExportShipmentData['supplier_id'] = $request['supplier_id'];
        $createrefundExportShipmentData['description'] = $refund['description'];
        $createrefundExportShipmentData['refund_price_totail'] = $refund['refund_price_totail'];
        $createrefundExportShipmentData['refund_totall_quantity'] = (int)$refund->refund_totall_quantity - (int)$request->refund_totall_quantity;

        if ($refundExportShipment = RefundExportShipment::query()->create($createrefundExportShipmentData)) {
            $refundDetails = $refund->refundExportShipmentDetail;

            $refundExportShipmentDetailDatas = $this->getRefundExportShipmentDetailData($refundExportShipment->id, $request->products, $refundDetails);

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
    }

    protected function GetRefundExportCode()
    {
        $latestExportShipment = RefundExportShipment::latest('id')->first(['id']);
        $latestId = $latestExportShipment->id ?? 0;

        return 'HH' . str_pad(++$latestId, 9, '0', STR_PAD_LEFT);
    }

    protected function getRefundExportShipmentDetailData($refundExportShipmentId, $products, $refundDetails)
    {
        $result = [];
        foreach ($products as $product) {
            foreach ($refundDetails as $refundDetail) {
                if ($refundDetail->product_id === $product['id']) {
                    $item['refund_export_shipment_id'] = $refundExportShipmentId;
                    $item['product_id'] = $product['id'];
                    $item['quantity'] = (int)$refundDetail->quantity - (int) $product['quantity'];
                    $item['export_price'] = $product['export_price'];
                    $item['refund_price'] = $product['refund_price'];
                    $item['lot_code'] = $product['lot_code'];
                    $result[] = $item;
                }
            }
        }

        return $result;
    }

    public function show($id)
    {
        return RefundExportShipment::query()->with('refundExportShipmentDetail')->find($id);
    }
}
