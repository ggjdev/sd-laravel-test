<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function rules()
    {
        $uniqueNameRule = Rule::unique('categories')->where(function (Builder $query): Builder {
            return $query->where('user_id', $this->user()->id);
        });

        return [
            'name' => ['required', 'max:40', $uniqueNameRule],
        ];
    }
}
