<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class RegisterRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        $maxDate = Carbon::now()->subYears(18)->format('Y-m-d');

        return [
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => 'required|string|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
            'birth_date' => 'required|date|before_or_equal:' . $maxDate
        ];
    }

        public function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'Duhet të jeni mbi 18 vjeç për t\'u regjistruar.',
            'password' => 'Fjalëkalimi duhet të jetë të paktën 8 karaktere dhe të përmbajë shkronjë kapitale, numër dhe simbol.',
            'email.unique' => 'Ky email ekziston në sistem.'
        ];
    }
}
