<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldUpdateRequest extends FormRequest
{
    //both admins and agent can post field update
    //agents can only update their assign fields 
        public function authorize(): bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'new_stage'=>['required','in:planted,growing,ready,harvested'],
            'notes'=>['nullable','string','max:2000'],
        ];
    }

    public function messages():array
    {
        return [
            'new_stage.required'=>'A stage is required.',
            'new_stage.in'=>'Stage must be one of:planted,growing,ready,harvested.',
            'notes.max'=>'Notes may not exceed 2000 character.'
        ];
    }

   
}
