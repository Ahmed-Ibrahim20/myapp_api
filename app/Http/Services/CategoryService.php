<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function indexCategory($search = null, $perPage = 10)
    {
        return $this->model->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function storeCategory(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'name',
                'slug',
                'note',
                'category_id',
                'user_add_id'
            ]);
            $data['user_add_id'] = Auth::id();

            // التعامل مع الصورة
            if (isset($requestData['image']) && $requestData['image'] instanceof \Illuminate\Http\UploadedFile) {
                $folder = public_path('category');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                $filename = uniqid('category_') . '.' . $requestData['image']->getClientOriginalExtension();
                $requestData['image']->move($folder, $filename);
                $data['image'] = 'category/' . $filename;
            }

            $category = $this->model->create($data);
            return [
                'status' => true,
                'message' => 'تم إنشاء القسم بنجاح',
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء القسم'
            ];
        }
    }

    public function editCategory($id)
    {
        return $this->model->find($id);
    }

    public function updateCategory(array $requestData, $id)
    {
        try {
            $category = $this->model->find($id);
            if (!$category) {
                return [
                    'status' => false,
                    'message' => 'القسم غير موجود'
                ];
            }

            $data = Arr::only($requestData, [
                'name',
                'slug',
                'category_id',
                'note'
            ]);

            // لو الصورة الجديدة موجودة
            if (isset($requestData['image']) && $requestData['image'] instanceof \Illuminate\Http\UploadedFile) {
                $folder = public_path('category');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                // حذف الصورة القديمة إن وجدت
                if (!empty($category->image) && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }

                $filename = uniqid('category_') . '.' . $requestData['image']->getClientOriginalExtension();
                $requestData['image']->move($folder, $filename);
                $data['image'] = 'category/' . $filename;
            } else {
                // لو مفيش صورة جديدة، نحتفظ بالقديمة
                $data['image'] = $category->image;
            }

            $category->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث القسم بنجاح',
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyCategory($id)
    {
        try {
            $category = $this->model->find($id);
            if (!$category) {
                return [
                    'status' => false,
                    'message' => 'القسم غير موجود'
                ];
            }

            // حذف الصورة من السيرفر إن وجدت
            if (!empty($category->image) && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $category->delete();

            return [
                'status' => true,
                'message' => 'تم حذف القسم بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Category deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف القسم'
            ];
        }
    }
}
