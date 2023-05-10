<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Requests\SupplierStore;
use App\Http\Requests\SupplierUpdate;
use App\Models\ExportShipment;
use App\Models\ImportShipment;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Supplier::query()->where('status', 1)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierStore $request)
    {
        $data = $request->validated();
        return Supplier::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierUpdate $request, Supplier $supplier)
    {
        $data = $request->validated();
        return $supplier->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($supplier)
    {
        $importShipments = ImportShipment::query()->where('supplier_id', $supplier)->get();
        $exportShipments = ExportShipment::query()->where('supplier_id', $supplier)->get();
        foreach ($importShipments as $importShipment) {
            $importShipment['supplier_id'] = 0;
            $importShipment->save();
        }
        foreach ($exportShipments as $exportShipment) {
            $exportShipment['supplier_id'] = 0;
            $exportShipment->save();
        }

        if (Supplier::destroy($supplier)) {
            return true;
        }
        return false;;
    }

    public function getCustomers()
    {
        return Supplier::query()->where('status', 2)->get();
    }
}
