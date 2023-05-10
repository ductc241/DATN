<?php

namespace App\Http\Requests;

use App\Rules\CheckLotcode;
use App\Rules\CheckProductRule;
use App\Rules\CheckQuantityRule;
use App\Rules\CheckSupplierRule;
use App\Rules\CheckUserRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExportShipmentRequest extends FormRequest
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
            'export_date' => [
                'required',
                'date_format:d/m/Y',
                'before:now'
            ],
            'supplier_id' => [
                'nullable',
                new CheckSupplierRule
            ],
            'user_id' => [
                'nullable',
                new CheckUserRule
            ],
            'products' => [
                'array'
            ],
            'products.*.id' => [
                'required',
                'integer',
                new CheckProductRule
            ],
            'products.*.quantity' => [
                'integer',
                'required',
            ],
            'products.*.price' => [
                'integer',
                'required'
            ],
            'products.*.lot_code' => [
                'string',
                'required',
            ]
        ];
    }

    public function messages()
    {
        return [
            'export_date.required' => 'Ngày xuất không được bỏ trống',
            'export_date.date_format' => 'Ngày xuất phải là dạng d/m/Y',
            'export_date.after' => 'Ngày xuất k được chọn ở tương lai',

            'products.*.id.required' => 'Sản phẩm không được bỏ trống',

            'products.*.quantity.integer' => 'Số lượng sản phẩm phải à số',
            'products.*.quantity.required' => 'Số lượng sản phẩm không được bỏ trống',

            'products.*.price.integer' => 'Giá sản phẩm phải à số',
            'products.*.price.required' => 'Giá sản phẩm không được bỏ trống',

            'products.*.lot_code.string' => 'Mã lô sản phẩm phải là 1 chuỗi kí tự',
            'products.*.lot_code.required' => 'Vui lòng chọn lô của sản phẩm',
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
