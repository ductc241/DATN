<?php

namespace App\Http\Controllers;

use App\http\Requests\AttributeProductRequest;
use App\Http\Requests\UpdateAttributeProductRequest;
use App\Http\Resources\UpdateAttributeProductResource;
use App\Models\AttributeProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AttributeProductController extends Controller
{
    public function index(Request $request)
    {
        return Response()->json(AttributeProduct::where('status', '=', 1)->paginate(10), 200);
    }

    public function save(AttributeProductRequest $request)
    {
        if (AttributeProduct::insert($request->all())) {
            return true;
        }
        return false;
    }

    public function getAttributeProduct($id)
    {
        return new UpdateAttributeProductResource(AttributeProduct::where('status', '=', 1)->find($id));
    }

    public function store($id, UpdateAttributeProductRequest $request)
    {
        return AttributeProduct::query()->find($id)->update($request->Validated());
    }

    public function delete($id)
    {
        if (!empty($id)) {
            $AttributeProduct = AttributeProduct::where('id', '=', $id);
            $data = [
                'status' => 0
            ];
            $AttributeProduct->update($data);
            return true;
        }
    }
}
