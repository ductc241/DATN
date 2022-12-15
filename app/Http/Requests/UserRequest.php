<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; 
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use App\Models\Role;
class UserRequest extends FormRequest
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
    public function rules(Request $request)
    { 
            return [
                'name' => [
                    'required',
                    'string', 
                    'max:255',
                    'min:3'
                ],   
                'email' => [
                    'required',
                    'unique:users' ,
                    'email',
                    'max:255',
                    'min:5'
                ],
                'password' => [
                    'required',
                    'max:255',
                    'min:5'
                ],
                'gender' => [
                    'required'
                ], 
                'role_id' => [
                    'required', 
                ], 
            ]; 
    }
    public function messages()
    {
        return [
            'name.required'=>'Tên tài khoản không được bỏ trống',
            'name.unique'=>'Tên tài khoản đã tồn tại',
            'name.max' => 'Tên tài khoản không quá 255 kí tự',
            'name.min' => 'Tên tài khoản tối thiểu 3 kí tự', 

            'email.required'=>'Email không được bỏ trống',
            'email.unique'=>'Email đã tồn tại',
            'email.max' => 'Email không quá 255 kí tự',
            'email.min' => 'Email tối thiểu 5 kí tự', 
            'email.email' => 'Nhập đúng định dạng email', 

            'role_id.required'=>'Vai trò không được bỏ trống',

            'password.required'=>'Mật khẩu không được bỏ trống',
            'password.max' => 'Mật khẩu không quá 255 kí tự',
            'password.min' => 'Mật khẩu tối thiểu 5 kí tự', 

            'gender.required'=>'Giới tính không được bỏ trống', 
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
 
