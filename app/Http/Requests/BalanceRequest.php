<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BalanceRequest extends FormRequest
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
            'balance' => 'required|regex:/^\d*(\.\d{1,11})?$/',
            'id' => 'required|integer'
        ];
    }

    protected function passedValidation(): void
    {
        $balance = $this->input('balance');
        $id = $this->input('id');

        $processedBalance = (float)$balance;
        $processedId = (int)$id;

        $this->replace([
            'balance' => $processedBalance,
            'id' => $processedId,
        ]);
    }
}
