<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
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
            'name' => 'nama kategori',
            'slug' => 'slug',
            'description' => 'deskripsi',
            'is_active' => 'status aktif',
        ];
    }
}
