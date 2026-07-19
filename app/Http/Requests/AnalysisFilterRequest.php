<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalysisFilterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'end_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
                'after_or_equal:start_date',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'start_date.before_or_equal' => 'Tanggal mulai tidak boleh melebihi hari ini.',
            'end_date.date' => 'Format tanggal akhir tidak valid.',
            'end_date.before_or_equal' => 'Tanggal akhir tidak boleh melebihi hari ini.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ];
    }
}
