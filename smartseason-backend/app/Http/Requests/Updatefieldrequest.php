<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Updatefieldrequest extends FormRequest
{
    //only admin can update field details
    //agents use fieldupdate controler@store to post stage update with notes
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules():array
    {
        return [
            'name'=>['sometimes','string','max:255'],
            'crop_type'=>['sometimes','string','max:255'],
            'planting_date'=>['sometimes','date'],
            'stage'=>['sometimes','in:planted,growing,ready,harvested']
        ];
    }

    public function messages():array
    {
        return [
            'planting_date.date'=>'planting date must be a valid date',
            'stage.in'=>'stage must be one of:planted,growing,ready,harvested.',
        ];
    }
   
}
