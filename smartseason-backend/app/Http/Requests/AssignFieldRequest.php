<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignFieldRequest extends FormRequest
{
    public function authorize():bool
    {
        return $this->user()->isAdmin();
    }

    public function rules():array
    {
        return [
            'agent_id'=>['required','integer',
            Rule::exists('users','id')->where('role','agent')]
        ];
    }
    
    public function messages():array
    {
        return [
            'agent_id.exists'=> 'The selected user does not 
            exists or is not a field agent.'
        ];
    }
}