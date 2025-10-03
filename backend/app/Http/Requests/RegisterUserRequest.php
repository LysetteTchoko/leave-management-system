<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Employer;

class RegisterUserRequest extends FormRequest
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
            'username' => 'required|min:2',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users,email',
            'photo' => 'nullable|image|'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function () {
            $email = $this->email;
            $employe = Employer::where('email', $email)->first();
            if (!$employe) {
                return response()->json([
                    'message' => 'Aucun employé trouvé avec cet email.',
                ]);
            }
        });
    }

    

   
}
