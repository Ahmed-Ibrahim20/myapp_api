<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Http\Services\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    protected OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $offers = $this->offerService->indexOffer($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة العروض',
            'data' => $offers
        ]);
    }

    public function store(OfferRequest $request)
    {
        $result = $this->offerService->storeOffer($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $offer = $this->offerService->editOffer($id);
        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'العرض غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل العرض',
            'data' => $offer
        ]);
    }

    public function update(OfferRequest $request, $id)
    {
        $result = $this->offerService->updateOffer($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->offerService->destroyOffer($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
