<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer');
        $commonRules = [
            'full_name' => 'required|string|max:100',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers')->ignore($customerId),
            ],
            'alternate_phone' => 'nullable|string|max:20',
            'country' => 'required|string|max:50',
            'address' => 'required|string',
            'note' => 'nullable|string',
            'user_add_id' => 'nullable|exists:users,id',
            'user_id' => 'nullable|exists:users,id',
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
            'full_name.required' => 'اسم العميل مطلوب.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
            'country.required' => 'الدولة مطلوبة.',
            'address.required' => 'العنوان مطلوب.',
            'user_add_id.exists' => 'المستخدم غير موجود.',
            'user_id.exists' => 'المستخدم غير موجود.'
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
