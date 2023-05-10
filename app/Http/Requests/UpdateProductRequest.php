<?php

namespace App\Http\Requests;

use App\Rules\CheckCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
                'unique:products',
                'max:100',
                'min:2'
            ],
            'category_id' => [
                new CheckCategory
            ],
            'price' => [
                'integer',
                'required'
            ],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên sản phẩm không được bỏ trống',
            'name.unique' => 'Tên sản phẩm đã tồn tại',
            'name.max' => 'Tên sản phẩm không quá 100 kí tự',
            'name.min' => 'Tên sản phẩm tối thiểu 2 kí tự',


            'price.required'=>'giá sản phẩm không được bỏ trống',
            'price.integer'=>'giá sản phẩm phải là số',
            'price.max' => 'giá sản phẩm không quá 10 kí tự',
            'price.min' => 'giá sản phẩm tối thiểu 3 kí tự',
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
