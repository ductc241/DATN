<?php

namespace App\Http\Controllers;

use App\Models\ExportShipment;
use App\Models\ExportShipmentDetail;
use App\Models\ImportShipment;
use App\Models\ImportShipmentDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductDetail;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticalController extends Controller
{
    public function show(Request $request)
    {
        $now = Carbon::now();
        $yesterday = Carbon::yesterday();
        $month_now = Carbon::now()->month;
        $days7_ago = Carbon::now()->day(-7);
        $now_year = $now->year;

        $salesMoneyInMonth = ExportShipment::query()->where('status', '=', 1)->whereMonth('created_at', $month_now)->orderBy('created_at')->get();
        $salesMoneyInYesterday = ExportShipment::where('status', '=', 1)->whereDay('created_at', $yesterday->day)->whereMonth('created_at', $yesterday->month)->whereYear('created_at', $yesterday->year)->sum('totall_price');
        $salesMoneyInNow = ExportShipment::where('status', '=', 1)->whereDay('created_at',  $now->day)->whereMonth('created_at', $month_now)->whereYear('created_at', $now_year)->sum('totall_price');
        $salesMoneyInDayOfWeek = ExportShipment::where('status', '=', 1)->whereBetween('created_at', [$days7_ago, $now])->sum('totall_price');

        $funds = ImportShipment::sum('import_price_totail');

        $bestSellingProducts = ExportShipmentDetail::select(
            'product_id',
            DB::raw('SUM(export_shipment_details.quantity) as total_quantity,SUM(export_shipments.totall_price) as totall_price')
        )
            ->leftJoin('products', 'products.id', '=', 'export_shipment_details.product_id')
            ->leftJoin('export_shipments', 'export_shipments.id', '=', 'export_shipment_details.export_shipment_id')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        $productTotail = Product::query()->sum('quantity');
        $salesInMonth = $now->month;
        if (!empty($request->month)) {
            $month = $request->month;
            $salesInMonth = ExportShipment::query()->whereMonth('created_at', $month)->orderBy('created_at')->groupBy('created_at')
                ->get();
        }

        $revenue = ExportShipment::select(
            DB::raw('SUM(totall_price) as total_price, DATE(created_at) as date')
        )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $result = [
            'sales_money_in_month' => $salesMoneyInMonth,
            'sales_in_month' => $salesInMonth,
            'sales_money_in_yesterday' => $salesMoneyInYesterday,
            'sales_money_in_now' => $salesMoneyInNow,
            'sales_money_in_day_ofWeek' => $salesMoneyInDayOfWeek,
            'funds' => $funds,
            'product_totail' => $productTotail,
            'best_selling_products' => $bestSellingProducts,
            'most_profitable_products' => $bestSellingProducts,
            'revenue' => $revenue
        ];

        return json_encode($result);
    }

    public function supplier(Request $request)
    {
        $supplierAll = ExportShipment::select(
            'supplier_id',
            DB::raw('SUM(export_shipments.totall_price) as total_price,SUM(export_shipments.quantity) as quantity')
        )
            ->leftJoin('suppliers', 'suppliers.id', '=', 'export_shipments.supplier_id')
            ->groupBy('supplier_id')
            ->limit(5)
            ->where('suppliers.status',1)
            ->with('supplier')
            ->get();

        $import_price_totail = ImportShipment::select(
            'supplier_id',
            DB::raw('SUM(import_shipments.import_price_totail) as import_price_totail,SUM(import_shipments.quantity) as quantity ')
        )
            ->leftJoin('suppliers', 'suppliers.id', '=', 'import_shipments.supplier_id')
            ->groupBy('supplier_id')
            ->limit(5)
            ->with('supplier')
            ->get();

        if (!empty($request->all())) {
            $data = $request->all();
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $supplier_id = $data['supplier_id'];

            $supplier = ExportShipment::whereBetween('export_date', [$from_date, $to_date])->where('supplier_id', '=', $supplier_id)->orderBy('export_date', 'ASC')->get();

            return json_encode($supplier);
        }

        $result = [
            'supplier_all' => $supplierAll,
            'import_price_totail' => $import_price_totail,
        ];
        return json_encode($result);
    }

    public function product(Request $request)
    {
        $ProductExportAll = ExportShipmentDetail::select(
            'product_id',
            'lot_code',
            DB::raw('SUM(export_shipment_details.price * export_shipment_details.quantity) as total_price,COUNT(export_shipment_details.export_shipment_id) as totail_order,SUM(export_shipment_details.quantity) as quantity')
        )
            ->leftJoin('products', 'products.id', '=', 'export_shipment_details.product_id')
            ->groupBy('lot_code', 'product_id')
            ->orderBy('total_price', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        if (!empty($request->all())) {

            $data = $request->all();
            $from_date = $data['from_date'];
            $lot_code = $data['lot_code'];
            $to_date = $data['to_date'];
            $product_id = $data['product_id'];

            $ProductDetail = ProductDetail::where('product_id', '=', $product_id)->where('lot_code', '=', $lot_code)->get();
            $ProductFiler = ExportShipmentDetail::whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $product_id)->where('lot_code', '=', $lot_code)->orderBy('created_at', 'ASC')->get();
            $totalProduct = ExportShipmentDetail::whereBetween('created_at', [$from_date, $to_date])->where('lot_code', '=', $lot_code)->where('product_id', '=', $product_id)->sum('quantity');

            foreach ($ProductFiler as $key => $iteam) {
                $interest = $iteam->price * $iteam->quantity;
                $interest += $interest;
            }
            $profit = $interest - $ProductDetail[0]['import_price'] * $totalProduct;
            $result = ['profit' => $profit, 'totalProduct' => $totalProduct, 'import_price' => $ProductDetail[0]['import_price'], 'product_filer' => $ProductFiler];
            return json_encode($result);
        }
        foreach ($ProductExportAll as $key => $value) {
            $importPrice = ProductDetail::query()->where('product_id', $value->product_id)->where('lot_code', $value->lot_code)->first();
            if (!$importPrice) {
                $value->profit = 0;
            } else {
                $totallImport = (float) $value->quantity * (int) $importPrice->import_price;
                $value->profit = (float)$value->total_price - (float) $totallImport;
            }
        }
        $result = $ProductExportAll;
        return json_encode($result);
    }

    public function inventoryProduct(Request $request)
    {
        $data = $request->all();
        $now = Carbon::now();

        if (empty($request->from_date && $request->to_date)) {
            $from_date = Carbon::now()->month(-3);
            $to_date = $now;
        } else {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
        }
        if (empty($request->product_id)) {
            $product = Product::where('status', '=', 1)->get();

            foreach ($product as $iteam) {
                $product = Product::where('status', '=', 1)->find($iteam->id);
                $quantity_import = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $iteam->id)->sum('quantity');
                $quantity_import_to_date = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $iteam->id)->sum('quantity');

                $quantity_export = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $iteam->id)->orderBy('created_at', 'ASC')->sum('quantity');
                $quantity_export_to_date = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $iteam->id)->sum('quantity');

                $beginning_inventory = $product->quantity + $quantity_export_to_date - $quantity_import_to_date;
                $ending_inventory = $beginning_inventory + $quantity_import - $quantity_export;
                $result[] = ['product' => $iteam, 'beginning_inventory' => $beginning_inventory, 'ending_inventory' => $ending_inventory, 'quantity_import' => $quantity_import, 'quantity_export' => $quantity_export];
            }
        } else {
            $product_id = $data['product_id'];

            $product = Product::where('status', '=', 1)->find($product_id);
            $quantity_import = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $product_id)->sum('quantity');
            $quantity_import_to_date = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $product_id)->sum('quantity');

            $quantity_export = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $product_id)->orderBy('created_at', 'ASC')->sum('quantity');
            $quantity_export_to_date = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $product_id)->sum('quantity');

            $beginning_inventory = $product->quantity + $quantity_export_to_date - $quantity_import_to_date;
            $ending_inventory = $beginning_inventory + $quantity_import - $quantity_export;

            $result = ['product' => $product, 'beginning_inventory' => $beginning_inventory, 'ending_inventory' => $ending_inventory, 'quantity_import' => $quantity_import, 'quantity_export' => $quantity_export];

            // tồn đầu kỳ = tồn thời gian hiện tại + số lượng xuất trong thời gian filer đầu tới hiện tại - số lượng nhập từ ngày hiện tại trở về ngày filer đầu
            // tồn cuối kỳ = tồn đầu kỳ + số lượng nhập trong kỳ - đi số lượng xuất trong kỳ
            // danh mục -> lấy từ product(id) ->
        }
        return json_encode($result);
    }

    public function inventorySupplier(Request $request)
    {
        $data = $request->all();
        $now = Carbon::now();
        if (empty($request->from_date && $request->to_date)) {
            $from_date = Carbon::now()->month(-3);
            $to_date = $now;
        } else {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
        }

        if (empty($request->supplier_id)) {

            $supplier = Supplier::where('status', '=', 1)->get();

            foreach ($supplier as $iteam) {
                $supplier = Supplier::where('status', '=', 1)->find($iteam->id);

                $superlier_quantity_export_all = ExportShipment::where('supplier_id', '=', $iteam->id)->sum('quantity');
                $superlier_quantity_import_all = ImportShipment::where('supplier_id', '=', $iteam->id)->sum('quantity');
                $supplier_quantity_export = ExportShipment::whereBetween('export_date', [$from_date, $to_date])->where('supplier_id', '=', $iteam->id)->get();
                $supplier_quantity_export_to_date = ExportShipment::whereBetween('export_date', [$from_date, $now])->where('supplier_id', '=', $iteam->id)->sum('quantity');
                $supplier_quantity_import = ImportShipment::whereBetween('import_date', [$from_date, $to_date])->where('supplier_id', '=', $iteam->id)->get();
                $supplier_quantity_import_to_date = ImportShipment::whereBetween('import_date', [$from_date, $now])->where('supplier_id', '=', $iteam->id)->sum('quantity');

                $superlier_quantity = $superlier_quantity_import_all - $superlier_quantity_export_all;
                $beginning_inventory = $superlier_quantity + $supplier_quantity_export_to_date - $supplier_quantity_import_to_date; // đầu
                $ending_inventory = $beginning_inventory + $supplier_quantity_import->sum('quantity') - $supplier_quantity_export->sum('quantity'); // cuối

                $result[] = ['supplier' => $iteam, 'superlier_quantity' => $superlier_quantity, 'beginning_inventory' => $beginning_inventory, 'ending_inventory' => $ending_inventory, 'supplier_import' => $supplier_quantity_import->sum('quantity'), 'supplier_export' => $supplier_quantity_export->sum('quantity')];
            }
        } else {
            $supplier_id = $data['supplier_id'];
            $supplier = Product::where('status', '=', 1)->find($supplier_id);

            $superlier_quantity_export_all = ExportShipment::where('supplier_id', '=', $supplier_id)->orderBy('export_date', 'ASC')->sum('quantity');
            $superlier_quantity_import_all = ImportShipment::where('supplier_id', '=', $supplier_id)->orderBy('export_date', 'ASC')->sum('quantity');
            $supplier_quantity_export = ExportShipment::whereBetween('export_date', [$from_date, $to_date])->where('supplier_id', '=', $supplier_id)->orderBy('export_date', 'ASC')->get();
            $supplier_quantity_export_to_date = ExportShipment::whereBetween('export_date', [$from_date, $now])->where('supplier_id', '=', $supplier_id)->orderBy('export_date', 'ASC')->sum('quantity');
            $supplier_quantity_import = ImportShipment::whereBetween('import_date', [$from_date, $to_date])->where('supplier_id', '=', $supplier_id)->orderBy('import_date', 'ASC')->get();
            $supplier_quantity_import_to_date = ImportShipment::whereBetween('import_date', [$from_date, $now])->where('supplier_id', '=', $supplier_id)->orderBy('import_date', 'ASC')->sum('quantity');

            $superlier_quantity = $superlier_quantity_import_all - $superlier_quantity_export_all;
            $beginning_inventory = $superlier_quantity_import_all - $superlier_quantity_export_all + $supplier_quantity_export_to_date - $supplier_quantity_import_to_date; // đầu
            $ending_inventory = $beginning_inventory + $supplier_quantity_import->sum('quantity') - $supplier_quantity_export->sum('quantity'); // cuối


            $result[] = ['supplier' => $supplier, 'superlier_quantity' => $superlier_quantity,  'beginning_inventory' => $beginning_inventory, 'ending_inventory' => $ending_inventory, 'supplier_import' => $supplier_quantity_import->sum('quantity'), 'supplier_export' => $supplier_quantity_export->sum('quantity')];
        }
        return json_encode($result);
    }

    public function inventoryCategory(Request $request)
    {
        $data = $request->all();
        $now = Carbon::now();

        if (empty($request->from_date && $request->to_date)) {
            $from_date = Carbon::now()->month(-3);
            $to_date = $now;
        } else {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
        }
        if (empty($request->id)) {
            $categorys = Category::where('status', '=', 1)->get();
            foreach ($categorys as $key => $iteam) {
                $category = Category::where('status', '=', 1)->find($iteam->id);
                $category_quantity = Product::where('status', '=', 1)->where('category_id', '=', $iteam->id)->get();

                foreach ($category_quantity as $iteam) {
                    $product = Product::where('category_id', '=', $iteam->category_id)->find($iteam->id);
                    $quantity_import = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $iteam->id)->sum('quantity');
                    $quantity_import_to_date = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $iteam->id)->sum('quantity');
                    $quantity_export = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $iteam->id)->orderBy('created_at', 'ASC')->sum('quantity');
                    $quantity_export_to_date = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $iteam->id)->sum('quantity');

                    $beginning_inventory = $product->quantity + $quantity_export_to_date - $quantity_import_to_date;
                    $ending_inventory = $beginning_inventory + $quantity_import - $quantity_export;
                    $result2[] = ['beginning_inventory' => $beginning_inventory, 'quantity_import' => $quantity_import, 'ending_inventory' => $ending_inventory,  'quantity_export' => $quantity_export];
                }
                if (!empty($result2)) {
                    $export_import = array_shift($result2);
                    foreach ($result2 as $value) {
                        $export_import = array_merge($export_import, $value);
                    }
                    foreach ($export_import as $key => &$value) {
                        $value = array_sum(array_column($result2, $key));
                    }
                    unset($value);
                };

                $result[] = ['category' => $category, 'category_quantity' => $category_quantity->sum('quantity'), 'export_import' => $export_import];
            }
        } else {
            $id = $data['id'];
            $category = Category::where('status', '=', 1)->find($id);
            $category_quantity = Product::where('status', '=', 1)->where('category_id', '=', $id)->get();

            foreach ($category_quantity as $iteam) {
                $product = Product::where('category_id', '=', $iteam->category_id)->find($iteam->id);
                $quantity_import = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $iteam->id)->sum('quantity');
                $quantity_import_to_date = ImportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $iteam->id)->sum('quantity');
                $quantity_export = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $to_date])->where('product_id', '=', $iteam->id)->orderBy('created_at', 'ASC')->sum('quantity');
                $quantity_export_to_date = ExportShipmentDetail::query()->whereBetween('created_at', [$from_date, $now])->where('product_id', '=', $iteam->id)->sum('quantity');

                $beginning_inventory = $product->quantity + $quantity_export_to_date - $quantity_import_to_date;
                $ending_inventory = $beginning_inventory + $quantity_import - $quantity_export;
                $result2[] = ['beginning_inventory' => $beginning_inventory, 'quantity_import' => $quantity_import, 'ending_inventory' => $ending_inventory,  'quantity_export' => $quantity_export];
            }
            if (!empty($result2)) {
                $export_import = array_shift($result2);
                foreach ($result2 as $value) {
                    $export_import = array_merge($export_import, $value);
                }
                foreach ($export_import as $key => &$value) {
                    $value = array_sum(array_column($result2, $key));
                }
                unset($value);
            };
            $result[] = ['category' => $category, 'category_quantity' => $category_quantity->sum('quantity'), 'export_import' => $export_import];
        }

        return json_encode($result);
    }
}
