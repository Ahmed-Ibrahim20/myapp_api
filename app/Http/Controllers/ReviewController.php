<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Http\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $reviews = $this->service->indexReview($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة التقييمات',
            'data' => $reviews
        ]);
    }

    public function show($id)
    {
        $review = $this->service->editReview($id);
        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'التقييم غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل التقييم',
            'data' => $review
        ]);
    }

    public function store(ReviewRequest $request)
    {
        $result = $this->service->storeReview($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function update(ReviewRequest $request, $id)
    {
        $result = $this->service->updateReview($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->service->destroyReview($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
