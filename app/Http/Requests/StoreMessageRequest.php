<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'message' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'name' => [
                'nullable',
                'string',
                'min:1',
                'max:20',
            ],
            'avatar' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
