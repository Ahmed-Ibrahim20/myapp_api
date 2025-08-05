<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storeId = $this->route('store');
        $commonRules = [
            'name' => 'required|string|max:100',
            'domain' => [
                'required',
                'string',
                'max:150',
                Rule::unique('stores')->ignore($storeId),
            ],
            'tech_stack' => 'nullable|array',
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
            'name.required' => 'اسم المتجر مطلوب.',
            'domain.required' => 'الدومين مطلوب.',
            'domain.unique' => 'الدومين مستخدم بالفعل.',
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
