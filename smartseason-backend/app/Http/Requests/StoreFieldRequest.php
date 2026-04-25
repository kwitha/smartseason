<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules():array
    {
        return[
            'name'=>['required','string','max:255'],
            'crop_type'=>['required','string','max:255'],
            'planting_date'=>['required','date'],
            'stage'=>['sometimes','in:planted,growing,ready,harvested'],
        ];
    }

    public function messages():array
    {
        return [
            'name.required'=>'Field name is required.',
            'crop_type.required'=>'crop type is required',
            'planting_date.required'=>'planting date must be a valid date.',
            'stage.in'=>'stage must be one of:planted,growing,ready,harvested.'
        ];
    }

   
}
