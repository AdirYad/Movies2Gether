<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimelineRequest extends FormRequest
{
    public function rules()
    {
        return [
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
            'seconds' => [
                'required',
                'numeric',
                'min:0',
                'max:20000',
            ],
            'is_played' => [
                'required',
                'boolean',
            ],
            'is_paused' => [
                'required',
                'boolean',
            ],
            'seeked' => [
                'required',
                'boolean',
            ],
        ];
    }
}
