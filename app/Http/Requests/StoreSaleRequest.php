<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'customer_id' => 'nullable|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'invoice_number' => 'required|string|max:255|unique:sales,invoice_number',
            'sale_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled',
            'payment_status' => 'required|in:unpaid,partially_paid,paid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.selling_price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah ada.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'in' => ':attribute yang dipilih tidak valid.',
            'array' => ':attribute harus berupa array.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min' => ':attribute minimal bernilai :min.',
            'exists' => ':attribute tidak ditemukan.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'pelanggan',
            'vehicle_id' => 'kendaraan',
            'payment_method_id' => 'metode pembayaran',
            'invoice_number' => 'nomor faktur',
            'sale_date' => 'tanggal penjualan',
            'discount' => 'diskon',
            'tax' => 'pajak',
            'status' => 'status',
            'payment_status' => 'status pembayaran',
            'items' => 'item penjualan',
            'items.*.product_id' => 'produk',
            'items.*.quantity' => 'kuantitas',
            'items.*.selling_price' => 'harga jual',
        ];
    }
}
