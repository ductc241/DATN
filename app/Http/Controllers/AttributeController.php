<?php

namespace App\Http\Controllers;
 
use App\http\Requests\AttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\UpdateAttributeRequest;
use App\Http\Resources\UpdateAttributeResource;

class AttributeController extends Controller
{
    public function index(Request $request)
    { 
        $name = $request->get('name');
        if($name){
            return Response()->json(Attribute::where('status', '=', 1)->where('name','like','%'.$name.'%')->paginate(10),200);   
        }else{
            return Response()->json(Attribute::where('status', '=', 1)->paginate(10),200); 
        }  
    }  
    public function save(AttributeRequest $request)
    {
        if (Attribute::insert($request->all())) {
            return true;
        }
        return false;
    }

    public function getAttribute($id)
    {
        return new UpdateAttributeResource(Attribute::where('status', '=', 1)->find($id));
    }

    public function store($id,UpdateAttributeRequest $request)
    {
        return Attribute::query()->find($id)->update($request->Validated());
    }
    public function delete($id) {
        if (!empty($id)) {
            $Attribute = Attribute::where('id', '=', $id);
            $data = [
                'status' => 0
            ];
            $Attribute->update($data);
            return true;
        }
    }
}