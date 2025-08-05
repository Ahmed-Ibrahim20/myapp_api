<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reviewId = $this->route('review');
        $commonRules = [
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
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
            'product_id.required' => 'المنتج مطلوب.',
            'product_id.exists' => 'المنتج غير موجود.',
            'customer_id.required' => 'العميل مطلوب.',
            'customer_id.exists' => 'العميل غير موجود.',
            'rating.required' => 'التقييم مطلوب.',
            'rating.integer' => 'التقييم يجب أن يكون رقم صحيح.',
            'rating.min' => 'أقل تقييم هو 1.',
            'rating.max' => 'أقصى تقييم هو 5.',
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
