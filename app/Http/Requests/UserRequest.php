<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array
     */
    public function rules()
    {
        
        return [
            'email' => 'required|unique:authentication_users,email',
            'password' => 'required',
            'is_superuser' => 'required',
            'username' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'is_staff' => 'required'
        ];
    }
    
}
