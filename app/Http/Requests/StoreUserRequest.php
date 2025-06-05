<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'regex:/^\d{11}$|^\d{14}$/', 'min:11','max:14','unique:users,document'],
            'shopkeeper' => ['boolean'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8'],
        ];

    }
    public function messages(): array
    {
        return [
            'name.required' => "O campo nome é obrigatorio",
            'name.string' => "O campo nome deve ser uma string",
            'name.max' => "O campo nome der no máximo 255 caracteres",

            'document.required' => "O campo documento <UNK> obrigatorio",
            'document.string' => "O campo documento deve ser uma string",
            'document.size' => "O campo deve ter 14 caracteres",
            'document.regex' => 'O documento deve conter 11 (CPF) ou 14 (CNPJ) dígitos numéricos.',
            'document.unique' => 'O documento deve ser único',

            'shopkeeper.boolean' => "O campo shopkeeper deve ser um boolean",


            'email.required' => "O campo email <UNK> obrigatorio",
            'email.string' => "O campo email deve ser uma string",
            'email.email' => "O campo email deve ser um email válido",
            'email.max' => "O campo email deve ter nó máximo 255 caracteres",
            'email.unique' => "O campo email <UNK> j<UNK> existe",

            'password.required' => "O campo senha <UNK> obrigatorio",
            'password.min' => "O campo senha deve ter no mínimo 8 caracteres",

        ];
    }
}
