<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $rules = [
            'address' => 'string|max:255',
            'date_of_birth' => 'date',
            // 'email' => 'email',
            'gender' => 'in:male,female,other',
            'image' => 'nullable',
            'name' => 'string|max:255',
            'nrc' => 'string|max:255',
            'phone' => 'string|max:255',
        ];

        return $rules;
    }
}
