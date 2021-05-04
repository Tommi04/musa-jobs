<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobOffersBySkillRequest extends FormRequest
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
            'skills'    => 'required|array|min:1',
            'skills.*.id'   => 'required|exists:skills,id',
            'skills.*.min_lvl'  => 'required|in: 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5',
            'skills.*.max_lvl'  => 'required|in: 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5|gte:skills.*.min_lvl',
            'skills.*.min_exp'  => 'required|integer|between:1,10'
        ];
    }
}
