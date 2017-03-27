<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::check()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment' => 'required',
            'parent_id' => 'required',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'is_question' => 'sometimes|required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'comment.required' => trans('comments.required'),
        ];
    }
}
