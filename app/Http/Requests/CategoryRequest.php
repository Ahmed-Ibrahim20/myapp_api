<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');
        $commonRules = [
            'name' => 'required|string|max:100',
            'slug' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_add_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
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
            'name.required' => 'اسم القسم مطلوب.',
            'name.max' => 'اسم القسم طويل جداً.',
            'slug.required' => 'السلاج مطلوب.',
            'slug.unique' => 'السلاج مستخدم بالفعل.',
            'image.image' => 'يجب أن يكون الصوره صورة.',
            'image.mimes' => 'صيغة الصوره غير مدعومة. الصيغ المسموحة: jpeg, png, jpg, gif, svg.',
            'image.max' => 'حجم الصوره يجب ألا يتجاوز 2 ميجابايت.',
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
