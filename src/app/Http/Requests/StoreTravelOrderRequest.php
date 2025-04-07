<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'requester_name' => 'required|string|max:255',
            'destination'    => 'required|string|max:255',
            'start_date'     => 'required|date|after_or_equal:today',
            'end_date'       => 'required|date|after:start_date'
        ];
    }

    public function messages()
    {
        return [
            'required'       => 'O campo :attribute é obrigatório',
            'after_or_equal' => 'A data de início deve ser hoje ou no futuro',
            'after'          => 'A data final deve ser após a data inicial'
        ];
    }
}
