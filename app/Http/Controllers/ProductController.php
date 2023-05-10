<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SearchProductRequest;
use App\Models\Product;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\productDetailResource;
use App\Http\Resources\UpdateProductResource;
use App\Models\productDetail;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ExportShipmentDetail;
use App\Models\ImportShipmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(SearchProductRequest $request)
    {
        $product =  Product::query()->when($request->keyword, function (Builder $query, string $keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')->orWhere('sku', 'like', '%' . $keyword . '%');
        })
        ->with('Category')
        ->get();

        return $product;
    }

    public function save(ProductRequest $request)
    {
        $product = new product();
        $product->fill($request->all());
        $product->image = $request->image;

        $KiTuDau = strtoupper(substr($product->Category->name, 0, 2));
        $KiTuRandom = Str::random(10);
        $sku = "$KiTuDau $KiTuRandom";
        $product->sku = $sku;

        if ($request->file('image')) {
            $product['image'] = $this->uploadFile($request->file('image'));
        }
        $product->save();
        return $product;
    }

    public function uploadFile($file)
    {
        $filename =  time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('imagesProduct', $filename,  'public');
    }

    public function getProduct($id)
    {
        return new UpdateProductResource(Product::where('status', '=', 1)->find($id));
    }

    public function store($id, UpdateProductRequest $request)
    {
        if ($request->file('image')) {
            $product['image'] = $this->uploadFile($request->file('image'));
        }
        return Product::query()->find($id)->update($request->all());
    }

    public function show($id)
    {
        $productDetail = ProductDetail::query()->where('product_id', $id)->get();
        return new productDetailResource($productDetail);
    }

    public function delete($id)
    {
        if (!empty($id)) {
            $Product = Product::where('id', '=', $id);
            $data = [
                'status' => 0
            ];
            $Product->update($data);
            return true;
        }
    }

    public function getProductDetail($id)
    {
        $productDetail = ProductDetail::query()->find($id);
        return $productDetail;
    }

    public function getProductHistory($id)
    {
        $importHistory = ImportShipmentDetail::query()->where('product_id', $id)->orderBy('created_at', 'desc')->get();

        $exportHistory = ExportShipmentDetail::query()->where('product_id', $id)->orderBy('created_at', 'desc')->get();

        $result['import_history'] = $importHistory;
        $result['export_history'] = $exportHistory;

        return $result;
    }

    public function getCountExportShipment(Request $request)
    {
        $fromDate = isset($request['from_date']) ? $request['from_date'] : '';
        $toDate = isset($request['to_date']) ? $request['to_date'] : '';
        $result = ExportShipmentDetail::select(
            'product_id',
            DB::raw('COUNT(export_shipment_details.id) as totail_export')
        )
            ->leftJoin('products', 'products.id', '=', 'export_shipment_details.product_id')
            ->groupBy('product_id')
            ->orderBy('totail_export', 'desc')
            ->when($fromDate && $toDate, function (Builder $query) use ($fromDate, $toDate) {
                $query->whereBetween('export_shipment_details.created_at', [$fromDate, $toDate]);
            })
            ->with('product')
            ->get();
        return $result;
    }
}
