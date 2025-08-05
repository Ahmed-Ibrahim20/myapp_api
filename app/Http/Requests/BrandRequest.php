<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand');
        $commonRules = [
            'name' => 'required|string|max:100',
            'slug' => [
                'required',
                'string',
                'max:150',
                Rule::unique('brands')->ignore($brandId),
            ],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'name.required' => 'اسم البراند مطلوب.',
            'name.max' => 'اسم البراند طويل جداً.',
            'slug.required' => 'السلاج مطلوب.',
            'slug.unique' => 'السلاج مستخدم بالفعل.',
            'logo.image' => 'يجب أن يكون الشعار صورة.',
            'logo.mimes' => 'صيغة الشعار غير مدعومة. الصيغ المسموحة: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'حجم الشعار يجب ألا يتجاوز 2 ميجابايت.',
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
