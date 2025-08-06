<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        $commonRules = [
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->ignore($productId),
            ],
            'name' => 'required|string|max:150',
            'slug' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('products')->ignore($productId),
            ],
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'details' => 'nullable|string',
            'price' => 'required|numeric',
            'compare_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|array',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_add_id' => 'nullable|exists:users,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            // صور المنتج
            'product_images' => 'nullable|array',
            'product_images.*.url' => 'required_with:product_images|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_images.*.alt_text' => 'nullable|string',
            'product_images.*.sort_order' => 'nullable|integer',
            // الفاريانت
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string',
            'variants.*.type' => 'required_with:variants|integer',
            'variants.*.values' => 'nullable|array',
            'variants.*.values.*.value' => 'required_with:variants.*.values|string',
            'variants.*.values.*.image_name' => 'nullable|string',
            'variants.*.values.*.color_name' => 'nullable|string',
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
            'name.required' => 'اسم المنتج مطلوب.',
            'price.required' => 'سعر المنتج مطلوب.',
            'sku.unique' => 'SKU مستخدم بالفعل.',
            'slug.unique' => 'Slug مستخدم بالفعل.',
            'images.image' => 'يجب أن يكون الصوره صورة.',
            'images.mimes' => 'صيغة الصوره غير مدعومة. الصيغ المسموحة: jpeg, png, jpg, gif, svg.',
            'images.max' => 'حجم الصوره يجب ألا يتجاوز 2 ميجابايت.',
            'product_images.*.  .required_with' => 'رابط الصورة مطلوب.',
            'variants.*.name.required_with' => 'اسم الفاريانت مطلوب.',
            'variants.*.type.required_with' => 'نوع الفاريانت مطلوب.',
            'variants.*.values.*.value.required_with' => 'قيمة الفاريانت مطلوبة.'
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
