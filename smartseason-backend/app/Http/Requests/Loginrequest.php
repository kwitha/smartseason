<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Loginrequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
     
    public function rules():array
    {
        return ['email' =>['required','email'],
                 'password' =>['required','string']
                 ];
    }

    public function messages():array
    {
        return[
            'email.required' =>'Email address is required.',
            'email.email' =>'please provide a valid email address.',
            'password.required' =>'password is required.'
        ];
    }
   
}
