<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
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
        $purchaseId = $this->route('purchase');

        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:255|unique:purchases,invoice_number,' . $purchaseId,
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'payment_status' => 'required|in:unpaid,partially_paid,paid',
            'payment_method_id' => 'required_if:payment_status,paid|nullable|exists:payment_methods,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
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
            'required_if' => ':attribute wajib diisi jika status pembayaran lunas.',
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
            'supplier_id' => 'pemasok',
            'invoice_number' => 'nomor faktur',
            'purchase_date' => 'tanggal pembelian',
            'status' => 'status',
            'payment_status' => 'status pembayaran',
            'payment_method_id' => 'metode pembayaran',
            'items' => 'item pembelian',
            'items.*.product_id' => 'produk',
            'items.*.quantity' => 'kuantitas',
            'items.*.cost_price' => 'harga beli',
        ];
    }
}
