<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:55|string|regex:/^[a-zA-Z]+$/u',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'name.max' => 'Name is too long!',
            'name.regex' => 'Name must be only letters!',
            'email.required' => 'Email is required!',
            'email.email' => 'Email is invalid!',
            'email.unique' => 'Email is already taken!',
            'password.required' => 'Password is required!',
            'password.min' => 'Password must be at least 6 characters!',
            'password.confirmed' => 'Password confirmation does not match!',
            'password_confirmation.required' => 'Password confirmation is required!',
            'password_confirmation.min' => 'Password confirmation must be at least 6 characters!',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
