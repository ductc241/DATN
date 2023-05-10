<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class StatisticalProductRequest extends FormRequest
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
            'from_date' => [
                'nullable',
                'string',
                'before:today'
            ],
            'to_date' => [
                'nullable',
                'string',
                'before:today'
            ],
            'product_id' => [
                'required',
                'integer'
            ],
            'lot_code' => [
                'required',
                'string'
            ]
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Id Sản phẩm không đươc bỏ trống ',
            'product_id.integer' => 'Id Sản phẩm phải là số',

            'lot_code.required' => 'Mã lô không đươc bỏ trống ',
            'lot_code.string' => 'Mã lô phải là số',

            'to_date.befor' => 'Ngày không được chọn ở tương lai',

            'from_date.before' => 'Ngày không được chọn ở tương lai',

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
