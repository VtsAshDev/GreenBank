<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'payer_id' => 'required|exists:users,id',
            'payee_id' => 'required|exists:users,id|different:payer_id',
            'value' => 'required|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return [
            'payer_id.required' => 'O campo pagador é obrigatório.',
            'payer_id.exists' => 'O pagador informado não existe.',

            'payee_id.required' => 'O campo recebedor é obrigatório.',
            'payee_id.exists' => 'O recebedor informado não existe.',
            'payee_id.different' => 'O recebedor deve ser diferente do pagador.',

            'value.required' => 'O valor da transferência é obrigatório.',
            'value.numeric' => 'O valor da transferência deve ser um número.',
            'value.min' => 'O valor da transferência deve ser maior que R$0,00.',
        ];
    }

}
