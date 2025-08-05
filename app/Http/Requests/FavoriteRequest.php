<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $favoriteId = $this->route('favorite');
        $commonRules = [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
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
            'user_id.required' => 'المستخدم مطلوب.',
            'user_id.exists' => 'المستخدم غير موجود.',
            'product_id.required' => 'المنتج مطلوب.',
            'product_id.exists' => 'المنتج غير موجود.',
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
