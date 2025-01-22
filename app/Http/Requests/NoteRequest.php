<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title'         => ['required', 'string'],
            'content'       => ['nullable', 'string']
        ];
    }
}
