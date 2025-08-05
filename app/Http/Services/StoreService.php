<?php

namespace App\Http\Services;

use App\Models\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreService
{
    protected Store $model;

    public function __construct(Store $model)
    {
        $this->model = $model;
    }

    public function indexStore($search = null, $perPage = 10)
    {
        return $this->model->with(['userAdd'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            })->paginate($perPage);
    }

    public function storeStore(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'name',
                'domain',
                'tech_stack',
                'user_add_id'
            ]);
            $data['user_add_id'] = Auth::id();
            $store = $this->model->create($data);
            return [
                'status' => true,
                'message' => 'تم إنشاء المتجر بنجاح',
                'data' => $store->load(['userAdd'])
            ];
        } catch (\Exception $e) {
            Log::error('Store creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء المتجر'
            ];
        }
    }

    public function editStore($id)
    {
        return $this->model->with(['userAdd'])->find($id);
    }

    public function updateStore(array $requestData, $id)
    {
        try {
            $store = $this->model->find($id);
            if (!$store) {
                return [
                    'status' => false,
                    'message' => 'المتجر غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'name',
                'domain',
                'tech_stack'
            ]);
            $store->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث المتجر بنجاح',
                'data' => $store->load(['userAdd'])
            ];
        } catch (\Exception $e) {
            Log::error('Store update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyStore($id)
    {
        try {
            $store = $this->model->find($id);
            if (!$store) {
                return [
                    'status' => false,
                    'message' => 'المتجر غير موجود'
                ];
            }
            $store->delete();
            return [
                'status' => true,
                'message' => 'تم حذف المتجر بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Store deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف المتجر'
            ];
        }
    }
}
