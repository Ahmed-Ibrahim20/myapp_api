<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon');
        $commonRules = [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons')->ignore($couponId),
            ],
            'discount_value' => 'required|numeric',
            'discount_type' => 'required|integer|in:0,1',
            'usage_limit' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'user_add_id' => 'nullable|exists:users,id',
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
            'code.required' => 'كود الكوبون مطلوب.',
            'code.unique' => 'كود الكوبون مستخدم بالفعل.',
            'discount_value.required' => 'قيمة الخصم مطلوبة.',
            'discount_type.required' => 'نوع الخصم مطلوب.',
            'usage_limit.required' => 'حد الاستخدام مطلوب.',
            'start_date.required' => 'تاريخ البداية مطلوب.',
            'end_date.required' => 'تاريخ النهاية مطلوب.',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية.',
            'user_add_id.exists' => 'المستخدم غير موجود.'
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
