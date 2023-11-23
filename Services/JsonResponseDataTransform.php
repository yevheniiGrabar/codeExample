<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JsonResponseDataTransform
{
    public const DEFAULT_PAGINATION_VALUE = '8';
    public const DEFAULT_PAGE_VALUE = '1';

    /**
     * @param Request $request
     * @param $collection
     * @return JsonResponse
     */
    public function conditionalResponse(Request $request, $collection): JsonResponse
    {
        if (!empty($request['reactive']) && $request['reactive'] == true || $request['reactive'] == null) {

            return $this->responseForClientSidePagination($collection);
        }

        return $this->responseForServerSidePagination($request, $collection);
    }

    /**
     * @param  $collection
     * @return JsonResponse
     */
    public function responseForClientSidePagination($collection): JsonResponse
    {
        return new JsonResponse(
            [
                'payload' => JsonResource::collection($collection->all())
            ]
        );
    }

    /**
     * @param Request $request
     * @param $collections
     * @return JsonResponse
     */
    public function responseForServerSidePagination(Request $request, $collections): JsonResponse
    {
        $pagination = json_decode($request->input('pagination'));

        return new JsonResponse(
            [
                'payload' =>
                    JsonResource::collection(
                        $collections->forPage(
                            $pagination->currentPage ?? self::DEFAULT_PAGE_VALUE,
                            $pagination->itemsPerPage ?? self::DEFAULT_PAGINATION_VALUE
                        )
                    ),
                'meta' => ['total' => $collections->count()]
            ]
        );
    }
}
