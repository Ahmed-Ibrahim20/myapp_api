<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FavoriteRequest;
use App\Http\Services\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected $service;

    public function __construct(FavoriteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10);
        $favorites = $this->service->indexFavorite($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'تم جلب المفضلات بنجاح',
            'data' => $favorites
        ]);
    }

    public function show($id)
    {
        $favorite = $this->service->editFavorite($id);
        if (!$favorite) {
            return response()->json([
                'status' => false,
                'message' => 'المفضلة غير موجودة'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تم جلب المفضلة بنجاح',
            'data' => $favorite
        ]);
    }

    public function store(FavoriteRequest $request)
    {
        $result = $this->service->storeFavorite($request->validated());
        $statusCode = $result['status'] ? 201 : 400;
        return response()->json($result, $statusCode);
    }

    public function update(FavoriteRequest $request, $id)
    {
        $result = $this->service->updateFavorite($request->validated(), $id);
        $statusCode = $result['status'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    public function destroy($id)
    {
        $result = $this->service->destroyFavorite($id);
        $statusCode = $result['status'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }
}
