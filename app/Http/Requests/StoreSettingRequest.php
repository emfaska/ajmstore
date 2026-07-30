<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_name'        => ['required', 'string', 'max:255'],
            'shop_address'     => ['nullable', 'string', 'max:500'],
            'shop_whatsapp'    => ['nullable', 'string', 'max:20'],
            'receipt_footer'   => ['nullable', 'string', 'max:500'],
            'default_tax'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_discount' => ['nullable', 'numeric', 'min:0'],
            'shop_logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop_name.required'  => 'Nama toko wajib diisi.',
            'default_tax.numeric' => 'Pajak default harus berupa angka.',
            'shop_logo.image'     => 'Logo harus berupa gambar.',
            'shop_logo.max'       => 'Ukuran logo maksimal 2MB.',
        ];
    }
}
