<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $categories = $this->categoryService->indexCategory($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة الأقسام',
            'data' => $categories
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $result = $this->categoryService->storeCategory($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $category = $this->categoryService->editCategory($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'القسم غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل القسم',
            'data' => $category
        ]);
    }

    public function update(CategoryRequest $request, $id)
    {
        $result = $this->categoryService->updateCategory($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->categoryService->destroyCategory($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
