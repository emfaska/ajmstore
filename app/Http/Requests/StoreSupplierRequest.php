<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'boolean' => ':attribute tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama supplier',
            'pic_name' => 'nama PIC',
            'phone' => 'nomor telepon',
            'email' => 'email',
            'address' => 'alamat',
            'description' => 'keterangan',
            'is_active' => 'status aktif',
        ];
    }
}
