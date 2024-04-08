<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }
    public function rules()
    {
        return [
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:1000',
            'status' => 'required|in:published,draft',
            'author_id' => 'required|integer|max:10'
        ];
    }
}
