<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TinkoffWebhookRequest extends FormRequest
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
            'TerminalKey' => 'string',
            'OrderId' => 'string',
            'Success' => 'boolean',
            'Status' => 'string',
            'PaymentId' => 'string',
            'ErrorCode' => 'string',
            'Amount' => 'string',
            'CardId' => 'string',
            'Pan' => 'string',
            'ExpDate' => 'string',
            'Token' => 'string',
        ];
    }
}
