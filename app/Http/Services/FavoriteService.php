<?php

namespace App\Http\Services;

use App\Models\Favorite;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\DB; // لم يعد مطلوبًا

class FavoriteService
{
    public function indexFavorite($search = null, $perPage = 10)
    {
        return Favorite::with(['user', 'product'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })->paginate($perPage);
    }

    public function editFavorite($id)
    {
        return Favorite::with(['user', 'product'])->find($id);
    }

    public function storeFavorite(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'user_id',
                'product_id',
            ]);
            $favorite = Favorite::create($data);
            return [
                'status' => true,
                'message' => 'تم إضافة المفضلة بنجاح',
                'data' => $favorite
            ];
        } catch (\Exception $e) {
            Log::error('Favorite creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة المفضلة'
            ];
        }
    }

    public function updateFavorite(array $requestData, $id)
    {
        try {
            $favorite = Favorite::find($id);
            if (!$favorite) {
                return [
                    'status' => false,
                    'message' => 'المفضلة غير موجودة'
                ];
            }
            $data = Arr::only($requestData, [
                'user_id',
                'product_id',
            ]);
            $favorite->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث المفضلة بنجاح',
                'data' => $favorite
            ];
        } catch (\Exception $e) {
            Log::error('Favorite update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyFavorite($id)
    {
        try {
            $favorite = Favorite::find($id);
            if (!$favorite) {
                return [
                    'status' => false,
                    'message' => 'المفضلة غير موجودة'
                ];
            }
            $favorite->delete();
            return [
                'status' => true,
                'message' => 'تم حذف المفضلة'
            ];
        } catch (\Exception $e) {
            Log::error('Favorite deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف المفضلة'
            ];
        }
    }
}
