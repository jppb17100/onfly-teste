<?php

namespace App\Http\Requests;

use App\Rules\ValidOrderStatusTransition;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTravelOrderRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'status' => 'required|in:aprovado,cancelado'
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'O campo status é obrigatório.',
            'status.string'   => 'O status deve ser um texto.',
            'status.in'       => 'Status inválido. Os valores permitidos são: aprovado ou cancelado.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first()
        ], 422));
    }
}
