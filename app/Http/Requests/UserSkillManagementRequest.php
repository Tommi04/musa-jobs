<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSkillManagementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'skill_id'      => 'required|exists:skills,id',
            'lvl'           => 'required|in: 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5',
            'exp_years'     => 'required|integer|between:1,10',
        ];
    }
}
