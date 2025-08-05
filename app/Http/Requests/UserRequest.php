<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ;

        $commonRules = [
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users')->ignore($userId),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'user_add_id' => 'nullable|exists:users,id',
            'role' => 'nullable|integer|between:0,2',
        ];

        if ($this->isMethod('POST')) {
            return array_merge($commonRules, [
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        }

        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            return array_merge($commonRules, [
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ]);
        }

        return [];
    }


    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.max' => 'يجب ألا يتجاوز الاسم 100 حرف.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً.',
            'email.max' => 'يجب ألا يتجاوز البريد الإلكتروني 150 حرف.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'phone.max' => 'يجب ألا يتجاوز رقم الهاتف 20 رقماً.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'role.between' => 'يجب أن تكون الصلاحية بين 0 لعملاء و1 لمزارعين و2 لمديرين.',
            'user_add_id.exists' => 'المستخدم المضيف غير موجود.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone)
            ]);
        }
    }
}
