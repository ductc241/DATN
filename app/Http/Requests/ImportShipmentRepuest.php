<?php

namespace App\Http\Requests;

use App\Rules\CheckProductRule;
use App\Rules\CheckSupplierRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportShipmentRepuest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'import_date' => [
                'required',
                'date_format:d/m/Y',
                'before:now'
            ],
            'supplier_id'=>[
                'nullable',
                new CheckSupplierRule
            ],
            'products'=>[
                'array'
            ],
            'products.*.id'=>[
                'required',
                'integer',
                new CheckProductRule
            ],
            'products.*.quantity'=>[
                'integer',
                'required'
            ],
            'products.*.import_price'=>[
                'integer',
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'import_date.required' => 'Ngày nhập không được bỏ trống',
            'import_date.date_format' => 'Ngày nhập phải là dạng d/m/Y',
            'import_date.after' => 'Ngày nhập k được chọn ở tương lai',

            'products.*.id.required'=>'Sản phẩm không được bỏ trống',

            'products.*.quantity.integer'=>'Số lượng sản phẩm phải à số',
            'products.*.quantity.required'=>'Số lượng sản phẩm không được bỏ trống',

            'products.*.import_price.integer'=>'Giá nhập sản phẩm phải à số',
            'products.*.import_price.required'=>'Giá nhập sản phẩm không được bỏ trống',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
