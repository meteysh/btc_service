<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|regex:/^\d*(\.\d{1,11})?$/',
            'id' => 'required|integer'
        ];
    }

    protected function passedValidation(): void
    {
        $amount = $this->input('amount');
        $id = $this->input('id');

        $processedAmount = (float)$amount;
        $processedId = (int)$id;

        $this->replace([
            'amount' => $processedAmount,
            'id' => $processedId,
        ]);
    }
}
