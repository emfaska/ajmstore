<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand');

        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $brandId,
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brandId,
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
            'unique' => ':attribute sudah digunakan.',
            'boolean' => ':attribute tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama brand',
            'slug' => 'slug',
            'description' => 'deskripsi',
            'is_active' => 'status aktif',
        ];
    }
}
