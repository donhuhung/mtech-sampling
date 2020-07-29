<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\Gifts;
use Mtech\Sampling\Models\Customers;
use Mtech\Sampling\Models\Projects;
use Mtech\API\Transformers\GiftTransformer;
use DB;

/**
 * Gift Back-end Controller
 */
class Gift extends General {

    protected $giftRepository;
    protected $customerRepository;
    protected $projectRepository;

    public function __construct(Gifts $gift, Customers $customner, Projects $project) {
        $this->giftRepository = $gift;
        $this->customerRepository = $customner;
        $this->projectRepository = $project;
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
    
    /**
     * @SWG\Post(
     *   path="/api/v1/gift/catch-gift",
     *   description="",
     *   summary="Catch Gift",
     *   operationId="api.v1.catchGift",
     *   produces={"application/json"},
     *   tags={"Gift"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Catch Gift",
     *     required=true,
     *    @SWG\Schema(example={
     *         "customer_id": 1,
     *         "arr_gift_id":"[1,2]"
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function catchGift(Request $request) {
        try {            
            $customnerID = $request->get('customer_id');
            $arrGiftId = (array)$request->get('arr_gift_id');
            $customner = $this->customerRepository->find($customnerID);
            $projectID = $customner->location->project_id;
            $project = $this->projectRepository->find($projectID);
            $locationId = $customner->location_id;
            $chooseGift = $project->allow_choose_gift;
            $numberReceiveGift = $project->number_receive_gift;
            if ($chooseGift) {
                //Choose Gift From Client
                if ($arrGiftId) {                       
                    foreach ($arrGiftId as $gift) {
                        Db::table('mtech_sampling_locations')->where('id', $locationId)->decrement('gift_inventory');
                        Db::table('mtech_sampling_gifts')->where('id', $gift)->decrement('gift_inventory');
                        $this->giftRepository->insertUserReceiveGift($customnerID, $gift, $locationId);
                    }
                }
            } else {
                //Random Gift On Server
                $gift = $this->giftRepository->randomGift($customnerID, $locationId, $numberReceiveGift);
                if (!$gift) {
                    return $this->respondWithError('Hết quà', self::HTTP_BAD_REQUEST);
                } 
                $arrGiftId = $gift;
            }
            $giftData = $this->giftRepository->whereIn('id', $arrGiftId)->get();
            $results = fractal($giftData, new GiftTransformer())->toArray();
            return $this->respondWithSuccess($results, ('Catch Gift successful!'));           
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

}
