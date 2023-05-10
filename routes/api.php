<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImportShipmentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeProductController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\ExportShipmentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderRefundController;
use App\Http\Controllers\RefundOrderSupplierController;
use App\Http\Controllers\StatisticalController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [LoginController::class, 'Login']);
Route::get('/login', [LoginController::class, 'index']);
Route::get('/logout', [LoginController::class, 'Logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('staff')->prefix('products')->group(function () {
        Route::get('/count-export', [ProductController::class, 'getCountExportShipment']);
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'save']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'store']);
        Route::get('/detail/{id}', [ProductController::class, 'getProduct']);
        Route::get('product-detail/{id}', [ProductController::class, 'getProductDetail']);
        Route::delete('/{id}', [ProductController::class, 'delete']);
        Route::get('/history/{id}', [ProductController::class, 'getProductHistory']);
    });

    // attributes
    Route::middleware('staff')->prefix('/user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'save']);
        Route::get('/{id}', [UserController::class, 'getUser']);
        Route::put('/{id}', [UserController::class, 'store']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });
    Route::middleware('staff')->prefix('attributes')->group(function () {
        Route::get('/', [AttributeController::class, 'index']);
        Route::post('/', [AttributeController::class, 'save']);
        Route::get('/{id}', [AttributeController::class, 'getAttribute']);
        Route::put('/{id}', [AttributeController::class, 'store']);
        Route::delete('/{id}', [AttributeController::class, 'delete']);
    });

    // attribute_product
    Route::middleware('staff')->prefix('attribute-products')->group(function () {
        Route::get('/', [AttributeProductController::class, 'index']);
        Route::post('/', [AttributeProductController::class, 'save']);
        Route::get('/{id}', [AttributeProductController::class, 'getAttributeProduct']);
        Route::put('/{id}', [AttributeProductController::class, 'store']);
        Route::delete('/{id}', [AttributeProductController::class, 'delete']);
    });

    Route::get('/customers', [SupplierController::class, 'getCustomers']);
    Route::middleware('staff')->resource('suppliers', SupplierController::class);

    Route::middleware('staff')->prefix('category')->group(function () {
        Route::get('/{id}', [CategoryController::class, 'getCategory']);
        Route::put('/{id}', [CategoryController::class, 'store']);
        Route::delete('/{id}', [CategoryController::class, 'delete']);
    });
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'save']);

    Route::middleware('staff')->prefix('import-shipment')->group(function () {
        Route::post('/', [ImportShipmentController::class, 'save']);
        Route::get('/{import_id}', [ImportShipmentController::class, 'getDetail']);
        Route::get('/', [ImportShipmentController::class, 'index']);
    });

    Route::middleware('staff')->prefix('export-shipment')->group(function () {
        Route::post('/', [ExportShipmentController::class, 'save']);
        Route::get('/{export_id}', [ExportShipmentController::class, 'getDetail']);
        Route::get('/', [ExportShipmentController::class, 'index']);
    });

    // Route::middleware('owner')->prefix('/statistical')->group(function () {
    Route::prefix('/statistical')->group(function () {
        Route::get('/', [StatisticalController::class, 'show']);
        Route::post('/product', [StatisticalController::class, 'product']);
        Route::post('/inventoryProduct', [StatisticalController::class, 'inventoryProduct']);
        Route::post('/inventorySupplier', [StatisticalController::class, 'inventorySupplier']);
        Route::post('/inventoryCategory', [StatisticalController::class, 'inventoryCategory']);
        Route::post('/supplier', [StatisticalController::class, 'supplier']);
    });

    Route::middleware('staff')->prefix('/refund-order')->group(function () {
        Route::get('/', [OrderRefundController::class, 'index']);
        Route::post('/', [OrderRefundController::class, 'store']);
        Route::get('/{refund_export_id}', [OrderRefundController::class, 'show']);
    });
    Route::post('/export-shipment', [OrderRefundController::class, 'SearchExportShipment']);

    Route::middleware('staff')->prefix('/refund-supplier')->group(function () {
        Route::get('/', [RefundOrderSupplierController::class, 'index']);
        Route::post('/', [RefundOrderSupplierController::class, 'save']);
        Route::get('/{id}', [RefundOrderSupplierController::class, 'show']);
    });
});
