<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Http\Services\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $stores = $this->storeService->indexStore($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة المتاجر',
            'data' => $stores
        ]);
    }

    public function store(StoreRequest $request)
    {
        $result = $this->storeService->storeStore($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $store = $this->storeService->editStore($id);
        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'المتجر غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل المتجر',
            'data' => $store
        ]);
    }

    public function update(StoreRequest $request, $id)
    {
        $result = $this->storeService->updateStore($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->storeService->destroyStore($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
