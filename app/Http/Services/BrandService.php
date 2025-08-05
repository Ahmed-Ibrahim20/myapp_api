<?php

namespace App\Http\Services;

use App\Models\Brand;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandService
{
    protected Brand $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    public function indexBrand($search = null, $perPage = 10)
    {
        return $this->model->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function storeBrand(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'name',
                'slug',
                'description',
                'user_add_id'
            ]);
            $data['user_add_id'] = Auth::id();

            // رفع صورة اللوجو إذا كانت موجودة
            if (isset($requestData['logo']) && $requestData['logo'] instanceof \Illuminate\Http\UploadedFile) {
                $folder = public_path('brand');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                $filename = uniqid('brand_') . '.' . $requestData['logo']->getClientOriginalExtension();
                $requestData['logo']->move($folder, $filename);
                $data['logo'] = 'brand/' . $filename;
            }

            $brand = $this->model->create($data);
            return [
                'status' => true,
                'message' => 'تم إنشاء البراند بنجاح',
                'data' => $brand
            ];
        } catch (\Exception $e) {
            Log::error('Brand creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء البراند'
            ];
        }
    }

    public function editBrand($id)
    {
        return $this->model->find($id);
    }

    public function updateBrand(array $requestData, $id)
    {
        try {
            $brand = $this->model->find($id);
            if (!$brand) {
                return [
                    'status' => false,
                    'message' => 'البراند غير موجود'
                ];
            }

            $data = Arr::only($requestData, [
                'name',
                'slug',
                'description'
            ]);

            // لو الصورة الجديدة موجودة (يعني فعلاً المستخدِم بعتهالك)
            if (isset($requestData['logo']) && $requestData['logo'] instanceof \Illuminate\Http\UploadedFile) {
                $folder = public_path('brand');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                // حذف الصورة القديمة
                if (!empty($brand->logo) && file_exists(public_path($brand->logo))) {
                    unlink(public_path($brand->logo));
                }

                $filename = uniqid('brand_') . '.' . $requestData['logo']->getClientOriginalExtension();
                $requestData['logo']->move($folder, $filename);
                $data['logo'] = 'brand/' . $filename;
            } else {
                // لو مفيش صورة جديدة مبعوتة، نحتفظ بالقديمة
                $data['logo'] = $brand->logo;
            }

            $brand->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث البراند بنجاح',
                'data' => $brand
            ];
        } catch (\Exception $e) {
            Log::error('Brand update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }


    public function destroyBrand($id)
    {
        try {
            $brand = $this->model->find($id);
            if (!$brand) {
                return [
                    'status' => false,
                    'message' => 'البراند غير موجود'
                ];
            }

            // حذف الصورة من السيرفر إن وجدت
            if (!empty($brand->logo) && file_exists(public_path($brand->logo))) {
                unlink(public_path($brand->logo));
            }

            $brand->delete();

            return [
                'status' => true,
                'message' => 'تم حذف البراند بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Brand deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف البراند'
            ];
        }
    }
}
