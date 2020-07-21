<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\Gifts;
use Mtech\API\Transformers\GiftTransformer;

/**
 * Gift Back-end Controller
 */
class Gift extends General {

    protected $giftRepository;

    public function __construct(Gifts $gift) {
        $this->giftRepository = $gift;
    }
    
    /**
     * @SWG\Post(
     *   path="/api/v1/gift/list",
     *   description="",
     *   summary="Get List Gift By Location",
     *   operationId="api.v1.getListGiftByLocation",
     *   produces={"application/json"},
     *   tags={"Gift"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Get List Gift By Location",
     *     required=true,
     *    @SWG\Schema(example={
     *         "location_id": 1
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function getListGift(Request $request) {
        try {
            $locationId = $request->get('location_id');
            $gifts = $this->giftRepository->where('location_id',$locationId)->where('total_gift','>',0)->get();
            $results = fractal($gifts, new GiftTransformer())->toArray();
            return $this->respondWithSuccess($results, ('Get List Gift successful!'));            
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

}
