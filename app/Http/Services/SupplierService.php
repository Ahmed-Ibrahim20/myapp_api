<?php

namespace App\Http\Services;

use App\Models\Supplier;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    public function indexSupplier($search = null, $perPage = 10)
    {
        return Supplier::when($search, function ($query) use ($search) {
            $query->where('full_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function editSupplier($id)
    {
        return Supplier::find($id);
    }

    public function storeSupplier(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'full_name',
                'phone',
                'alternate_phone',
                'total',
                'country',
                'address',
                'note',
                'user_add_id',
            ]);
            $data['user_add_id'] = Auth::id();
            $supplier = Supplier::create($data);
            return [
                'status' => true,
                'message' => 'تم إضافة المورد بنجاح',
                'data' => $supplier
            ];
        } catch (\Exception $e) {
            Log::error('Supplier creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة المورد'
            ];
        }
    }

    public function updateSupplier(array $requestData, $id)
    {
        try {
            $supplier = Supplier::find($id);
            if (!$supplier) {
                return [
                    'status' => false,
                    'message' => 'المورد غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'full_name',
                'phone',
                'alternate_phone',
                'total',
                'country',
                'address',
                'note',
                'user_add_id',
            ]);
            $supplier->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث بيانات المورد',
                'data' => $supplier
            ];
        } catch (\Exception $e) {
            Log::error('Supplier update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroySupplier($id)
    {
        try {
            $supplier = Supplier::find($id);
            if (!$supplier) {
                return [
                    'status' => false,
                    'message' => 'المورد غير موجود'
                ];
            }
            $supplier->delete();
            return [
                'status' => true,
                'message' => 'تم حذف المورد'
            ];
        } catch (\Exception $e) {
            Log::error('Supplier deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف المورد'
            ];
        }
    }
}
