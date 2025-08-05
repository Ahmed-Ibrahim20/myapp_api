<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $offerId = $this->route('offer');
        $commonRules = [
            'user_add_id' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => 'required|numeric',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ];
        if ($this->isMethod('POST')) {
            return $commonRules;
        }
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            return $commonRules;
        }
        return [];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان العرض مطلوب.',
            'discount_type.required' => 'نوع الخصم مطلوب.',
            'discount_type.in' => 'نوع الخصم غير صحيح.',
            'value.required' => 'قيمة الخصم مطلوبة.',
            'end_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
