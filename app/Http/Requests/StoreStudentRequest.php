<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan', 
            'school_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'special_needs' => 'nullable|string|max:255',
            'diagnosis_notes' => 'nullable|string',
            'parent_phone' => 'nullable|numeric|digits_between:9,15',
            'parent_id' => 'nullable|exists:users,id',
        ];
    }
}
