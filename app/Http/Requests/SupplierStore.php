<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class SupplierStore extends FormRequest
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
            'name' => [
                'required',
                'string',
                'unique:suppliers',
                'max:100',
                'min:1'
            ],
            'address' => [
                'required',
                'string',
                'unique:suppliers',
                'max:100',
                'min:1'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên nhà cung cấp không được bỏ trống',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại vui lòng chọn tên khác',
            'name.max' => 'Tên chỉ đc nhập không quá 100 kí tự',
            'name.min' => 'Tên phải có ít nhất 1 kí tự',

            'address.required' => 'Tên nhà cung cấp không được bỏ trống',
            'address.unique' => 'Tên nhà cung cấp đã tồn tại vui lòng chọn tên khác',
            'address.max' => 'Tên chỉ đc nhập không quá 100 kí tự',
            'address.min' => 'Tên phải có ít nhất 1 kí tự'
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
