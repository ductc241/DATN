<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; 
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttributeRequest extends FormRequest
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
          'name'=> [
            'required',
            'string',
            'unique:attributes',
            'max:20',
            'min:2'
          ]
        ];
    }

    public function messages()
    {
        return [
            'name.required'=>'Tên thuộc tính không được bỏ trống',
            'name.unique'=>'Tên thuộc tính đã tồn tại vui lòng chọn tên khác',
            'name.max' => 'Tên thuộc tính chỉ được nhập không quá 20 kí tự',
            'name.min' => 'Tên thuộc tính phải có ít nhất 2 kí tự'
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
