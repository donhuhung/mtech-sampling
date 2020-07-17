<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\Customers;
use Mtech\Sampling\Models\CustomerGifts;
use Mtech\Sampling\Models\Gifts;
use Mtech\API\Transformers\GiftTransformer;
use Lang;
use JWTAuth;
use Mtech\API\Classes\HelperClass;

/**
  /**
 * Customer Back-end Controller
 */
class Customer extends General {

    protected $customerRepository;
    protected $customerGiftRepository;
    protected $giftRepository;

    public function __construct(Customers $customer, CustomerGifts $customerGift, Gifts $gift) {
        $this->customerRepository = $customer;
        $this->customerGiftRepository = $customerGift;
        $this->giftRepository = $gift;
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/customer/store",
     *   description="",
     *   summary="Store Customer",
     *   operationId="api.v1.postStoreCustomer",
     *   produces={"application/json"},
     *   tags={"Customer"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="gender: 1-Male, 2-Female, product_sampling:Get from API (/api/v1/general/get-product-sampling)",
     *     required=true,
     *    @SWG\Schema(example={
     *         "name": "Nguyễn Văn A",
     *         "gender": 1,
     *         "dob": "05-02-1990",
     *         "phone": "0903123456",
     *         "cmnd": "025589978",
     *         "address": "123 Lạc Long Quân, Tân Bình",
     *         "brand_in_use": "Unilever",
     *         "product": "Clear",
     *         "product_sampling": 1,
     *         "location_id": 1,
     *         "otp": "123456789",
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     * security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function storeCustomer(Request $request) {
        try {
            $now = date('Y-m-d H:i:s');
            $name = $request->get('name') ? $request->get('name') : '';
            $gender = $request->get('gender') ? $request->get('gender') : 0;
            $dob = $request->get('dob') ? $request->get('dob') : '';
            $phone = $request->get('phone') ? $request->get('phone') : '';
            $cmnd = $request->get('cmnd') ? $request->get('cmnd') : '';
            $address = $request->get('address') ? $request->get('address') : '';
            $brandInUse = $request->get('brand_in_use') ? $request->get('brand_in_use') : '';
            $product = $request->get('product') ? $request->get('product') : '';
            $productSampling = $request->get('product_sampling') ? $request->get('product_sampling') : 0;
            $locationId = $request->get('location_id');
            $otp = $request->get('otp');            
            $customer = "";
            if ($phone) {
                $customer = $this->customerRepository->where('phone', $phone)->where('otp', $otp)->first();
            }
            if ($customer) {
                return $this->respondWithError('Số điện thoại này đã nhận quà từ chương trình', self::HTTP_BAD_REQUEST);
            }
            //Store Customer
            $arrCustomer = ['name' => $name, 'cmnd' => $cmnd, 'dob' => $dob,
                'gender' => $gender, 'phone' => $phone, 'address' => $address,'otp' => $otp,
                'brand_in_use' => $brandInUse, 'product_name' => $product, 'product_sampling' => $productSampling,
                'location_id' => $locationId, 'created_at' => $now];            
            $customerInfo = $this->customerRepository->create($arrCustomer);
            $customerId = $customerInfo->id;
            
            //Random Gift
            $gift = $this->giftRepository->randomGift($customerId, $locationId);
            if(!$gift){
                return $this->respondWithError('Hết quà', self::HTTP_BAD_REQUEST);
            }
            $giftData = $this->giftRepository->find($gift);                        
            $results = fractal($giftData, new GiftTransformer())->toArray();
            return $this->respondWithSuccess($results, ('Store Customer successful!'));
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

}
