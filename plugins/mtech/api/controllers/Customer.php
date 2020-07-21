<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\Customers;
use Mtech\Sampling\Models\CustomerGifts;
use Mtech\Sampling\Models\Gifts;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
use Mtech\API\Transformers\GiftTransformer;
use Lang;
use JWTAuth;
use Mtech\API\Classes\HelperClass;
use DB;

/**
  /**
 * Customer Back-end Controller
 */
class Customer extends General {

    protected $projectRepository;
    protected $customerRepository;
    protected $customerGiftRepository;
    protected $giftRepository;
    protected $locationRepository;

    public function __construct(Customers $customer, CustomerGifts $customerGift, Gifts $gift, Projects $project, Locations $location) {
        $this->customerRepository = $customer;
        $this->customerGiftRepository = $customerGift;
        $this->giftRepository = $gift;
        $this->projectRepository = $project;
        $this->locationRepository = $location;
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/customer/store",
     *   description="Store Customer",
     *   summary="Store Customer",
     *   produces={"application/json"},
     *   tags={"Customer"},
     *   @SWG\Parameter(
     *         name="name",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="gender",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="dob",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="phone",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ), 
     *   @SWG\Parameter(
     *         name="cmnd",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="address",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="brand_in_use",
     *         in="formData",     
     *         required=false,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="product",
     *         in="formData",     
     *         required=false,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="product_sampling",
     *         in="formData",     
     *         required=false,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="location_id",
     *         in="formData",     
     *         required=false,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="otp",
     *         in="formData",     
     *         required=false,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="arr_gift_id",
     *         in="formData",     
     *         required=false,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="customer_avatar",
     *         in="formData",
     *         description="Customer Avarta",
     *         required=true,
     *         type="file"
     *   ),
     *   @SWG\Parameter(
     *         name="bill_image",
     *         in="formData",
     *         description="Bill Image",
     *         required=true,
     *         type="file"
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
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
            $arrGiftId = $request->get('arr_gift_id');
            $otp = $request->get('otp');
            $customer = "";
            $customerAvatar = $request->file('customer_avatar');
            $billImage = $request->file('bill_image');
            if ($phone) {
                $customer = $this->customerRepository->where('phone', $phone)->where('otp', $otp)->first();
            }
            if ($customer) {
                return $this->respondWithError('Số điện thoại này đã nhận quà từ chương trình', self::HTTP_BAD_REQUEST);
            }
            
            //Get Info Project 
            $locationData = $this->locationRepository->find($locationId);
            $projectId = $locationData->id;
            $project = $this->projectRepository->find($projectId);
            
            
            if ($customerAvatar->isValid() && $billImage->isValid()) {
                $now = date('d-m-Y');
                $prefixName = $phone."_".$name;
                $fileName = HelperClass::convert_vi_to_en($prefixName);
                $fileName = preg_replace('/\s+/', '_', $fileName);
                $destinationPath = storage_path('app/media/' . $now . '/'.$projectId.'/'.$locationId.'/');                
                $fileNameAvatar = $fileName."_avatar.png";
                $fileNameBill = $fileName."_bill.png";
                $customerAvatar->move($destinationPath, $fileNameAvatar);
                $billImage->move($destinationPath, $fileNameBill);
            }
            //Store Customer
            $arrCustomer = ['name' => $name, 'cmnd' => $cmnd, 'dob' => $dob,
                'gender' => $gender, 'phone' => $phone, 'address' => $address, 'otp' => $otp,
                'brand_in_use' => $brandInUse, 'product_name' => $product, 'product_sampling' => $productSampling,
                'location_id' => $locationId,'file_name_avatar' => $fileNameAvatar,'file_name_bill' => $fileNameBill,'created_at' => $now];
            $customerInfo = $this->customerRepository->create($arrCustomer);
            $customerId = $customerInfo->id;            
            
            $chooseGift = $project->allow_choose_gift;
            $numberReceiveGift = $project->number_receive_gift;
            if ($chooseGift) {
                //Choose Gift From Client
                if ($arrGiftId) {
                    foreach ($arrGiftId as $gift) {
                        Db::table('mtech_sampling_locations')->where('id', $locationId)->decrement('total_gift');
                        Db::table('mtech_sampling_gifts')->where('id', $gift)->decrement('total_gift');
                        $this->giftRepository->insertUserReceiveGift($customerId, $gift, $locationId);
                    }
                }
            } else {
                //Random Gift On Server
                $gift = $this->giftRepository->randomGift($customerId, $locationId, $numberReceiveGift);
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
