<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier');
        $commonRules = [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'alternate_phone' => 'nullable|string|max:50',
            'total' => 'nullable|numeric|min:0',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'note' => 'nullable|string',
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
            'full_name.required' => 'اسم المورد مطلوب.',
            'full_name.max' => 'اسم المورد طويل جداً.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.max' => 'رقم الهاتف طويل جداً.',
            'alternate_phone.max' => 'رقم الهاتف البديل طويل جداً.',
            'total.numeric' => 'إجمالي الحساب يجب أن يكون رقم.',
            'country.max' => 'اسم الدولة طويل جداً.',
            'address.max' => 'العنوان طويل جداً.',
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
