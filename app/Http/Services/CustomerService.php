<?php

namespace App\Http\Services;

use App\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected Customer $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function indexCustomer($search = null, $perPage = 10)
    {
        return $this->model->when($search, function ($query) use ($search) {
            $query->where('full_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function storeCustomer(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'full_name',
                'phone',
                'alternate_phone',
                'country',
                'address',
                'note',
                'user_add_id',
                'user_id'
            ]);
            $data['user_add_id'] = Auth::id();
            $customer = $this->model->create($data);
            return [
                'status' => true,
                'message' => 'تم إنشاء العميل بنجاح',
                'data' => $customer
            ];
        } catch (\Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء العميل'
            ];
        }
    }

    public function editCustomer($id)
    {
        return $this->model->find($id);
    }

    public function updateCustomer(array $requestData, $id)
    {
        try {
            $customer = $this->model->find($id);
            if (!$customer) {
                return [
                    'status' => false,
                    'message' => 'العميل غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'full_name',
                'phone',
                'alternate_phone',
                'country',
                'address',
                'note',
                'user_id'
            ]);
            $customer->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث العميل بنجاح',
                'data' => $customer
            ];
        } catch (\Exception $e) {
            Log::error('Customer update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyCustomer($id)
    {
        try {
            $customer = $this->model->find($id);
            if (!$customer) {
                return [
                    'status' => false,
                    'message' => 'العميل غير موجود'
                ];
            }
            $customer->delete();
            return [
                'status' => true,
                'message' => 'تم حذف العميل بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Customer deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف العميل'
            ];
        }
    }
}
