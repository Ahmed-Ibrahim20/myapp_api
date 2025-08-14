<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Product;

class InterfaceService
{
    /**
     * جلب كل المنتجات (paginated) مع تحويل مناسب للواجهة.
     *
     * @param int $perPage
     * @param string|null $search
     * @param int|null $categoryId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllProducts($perPage = 10, $search = null, $categoryId = null)
    {
        $query = Product::with(['images', 'variants.values', 'category']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage);

        $collection = $paginator->getCollection()->map(function ($prod) {
            return $this->transformProductForInterface($prod);
        });

        $paginator->setCollection($collection);

        return $paginator;
    }
    /**
     * يرجع رابط كامل للصورة اعتماداً على APP_URL أو قيمة افتراضية.
     */
    protected function getFullUrl(?string $path): ?string
    {
        if (!$path) return null;
        // لو Path أصلاً رابط كامل
        if (preg_match('/^https?:\\/\\//', $path)) return $path;

        $appUrl = config('app.url') ?: env('APP_URL') ?: 'https://myappapi.fikriti.com';
        return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * جلب كل الأقسام مع عدد المنتجات لكل قسم
     */
    public function getAllCategories()
    {
        $categories = Category::withCount('products')->get();

        return $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'note' => $cat->note,
                'image' => $cat->image,
                'image_url' => $this->getFullUrl($cat->image),
                'products_count' => $cat->products_count,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ];
        })->values();
    }

    /**
     * تفاصيل قسم واحد (مع رابط الصورة).
     */
    public function getCategoryById($id)
    {
        $cat = Category::find($id);
        if (!$cat) return null;

        return [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'note' => $cat->note,
            'image' => $cat->image,
            'image_url' => $this->getFullUrl($cat->image),
            'created_at' => $cat->created_at,
            'updated_at' => $cat->updated_at,
        ];
    }

    /**
     * جلب منتجات قسم مُعيّن (paginated) وتحويل كل منتج للهيئة المناسبة للواجهة.
     */
    public function getProductsByCategory($categoryId, $perPage = 10, $search = null)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return [
                'category' => null,
                'products' => null
            ];
        }

        $query = Product::where('category_id', $categoryId)
            ->with(['images', 'variants.values']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage);

        $collection = $paginator->getCollection()->map(function ($prod) {
            $images = [];
            if (is_iterable($prod->images) && count($prod->images) > 0) {
                foreach ($prod->images as $img) {
                    $images[] = [
                        'id' => $img->id,
                        'url' => $img->url,
                        'full_url' => $this->getFullUrl($img->url),
                        'alt_text' => $img->alt_text,
                        'sort_order' => $img->sort_order,
                    ];
                }
            }

            $mainImage = count($images) ? $images[0]['full_url'] : ($prod->main_image ? $this->getFullUrl($prod->main_image) : null);

            $variants = [];
            if (is_iterable($prod->variants) && count($prod->variants) > 0) {
                foreach ($prod->variants as $v) {
                    $variants[] = [
                        'id' => $v->id,
                        'name' => $v->name,
                        'type' => $v->type,
                        'values' => is_iterable($v->values) ? $v->values->map(function ($val) {
                            return [
                                'id' => $val->id,
                                'value' => $val->value,
                                'image_name' => $val->image_name,
                                'color_name' => $val->color_name,
                            ];
                        })->values() : [],
                    ];
                }
            }

            return [
                'id' => $prod->id,
                'sku' => $prod->sku,
                'name' => $prod->name,
                'slug' => $prod->slug,
                'description' => $prod->description,
                'price' => $prod->price,
                'compare_price' => $prod->compare_price,
                'cost_price' => $prod->cost_price,
                'weight' => $prod->weight,
                'dimensions' => $prod->dimensions,
                'main_image' => $mainImage,
                'images' => $images,
                'details' => $prod->details,
                'features' => $prod->features,
                'user_add_id' => $prod->user_add_id,
                'supplier_id' => $prod->supplier_id,
                'category_id' => $prod->category_id,
                'brand_id' => $prod->brand_id,
                'created_at' => $prod->created_at,
                'updated_at' => $prod->updated_at,
                'variants' => $variants,
            ];
        });

        $paginator->setCollection($collection);

        return [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'note' => $category->note,
                'image' => $category->image,
                'image_url' => $this->getFullUrl($category->image),
            ],
            'products' => $paginator,
        ];
    }
    public function getProductById($id)
    {
        $prod = Product::with(['images', 'variants.values', 'category'])->find($id);
        if (!$prod) return null;
        return $this->transformProductForInterface($prod);
    }

    /**
     * تحويل Product Model لشكل مناسب للـ interface.
     */
    protected function transformProductForInterface($prod)
    {
        // تحضير صور المنتج كـ array من الكائنات
        $images = [];
        if (is_iterable($prod->images) && count($prod->images) > 0) {
            foreach ($prod->images as $img) {
                $images[] = [
                    'id' => $img->id,
                    'url' => $img->url,
                    'full_url' => $this->getFullUrl($img->url),
                    'alt_text' => $img->alt_text,
                    'sort_order' => $img->sort_order,
                ];
            }
        }

        // main image: أولوية لأول صورة فرعية، وإلا main_image العمود، وإلا null
        $mainImage = null;
        if (count($images) > 0) {
            $mainImage = $images[0]['full_url'];
        } elseif (!empty($prod->main_image)) {
            $mainImage = $this->getFullUrl($prod->main_image);
        } elseif (!empty($prod->image) && is_string($prod->image)) {
            // fallback لو عمود اسمه image موجود (legacy)
            $mainImage = $this->getFullUrl($prod->image);
        }

        // تحويل المتغيرات (variants)
        $variants = [];
        if (is_iterable($prod->variants) && count($prod->variants) > 0) {
            foreach ($prod->variants as $v) {
                $variants[] = [
                    'id' => $v->id,
                    'name' => $v->name,
                    'type' => $v->type,
                    'values' => is_iterable($v->values) ? $v->values->map(function ($val) {
                        return [
                            'id' => $val->id,
                            'value' => $val->value,
                            'image_name' => $val->image_name,
                            'color_name' => $val->color_name,
                        ];
                    })->values() : [],
                ];
            }
        }

        // معلومات القسم المصاحب (إن وُجد)
        $category = null;
        if ($prod->relationLoaded('category') && $prod->category) {
            $category = [
                'id' => $prod->category->id,
                'name' => $prod->category->name,
                'slug' => $prod->category->slug,
                'image_url' => $this->getFullUrl($prod->category->image ?? null),
            ];
        }

        return [
            'id' => $prod->id,
            'sku' => $prod->sku,
            'name' => $prod->name,
            'slug' => $prod->slug,
            'description' => $prod->description,
            'price' => $prod->price,
            'compare_price' => $prod->compare_price,
            'cost_price' => $prod->cost_price,
            'weight' => $prod->weight,
            'dimensions' => $prod->dimensions,
            'main_image' => $mainImage,
            'images' => $images,
            'details' => $prod->details,
            'features' => $prod->features,
            'user_add_id' => $prod->user_add_id,
            'supplier_id' => $prod->supplier_id,
            'category' => $category,
            'brand_id' => $prod->brand_id,
            'created_at' => $prod->created_at,
            'updated_at' => $prod->updated_at,
            'variants' => $variants,
        ];
    }
}
