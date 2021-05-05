<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
                'first_name'        => 'required|max:255',
                'last_name'         => 'required|max:255',
                'email'             => 'required|email|max:255|unique:users,email',
                'password'          => 'required|min:8|max:255|alpha_num',
                'confirm_password'  => 'required|same:password'
        ];
    }
}
