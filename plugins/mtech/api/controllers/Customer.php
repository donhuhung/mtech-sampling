<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\Customers;
use Mtech\Sampling\Models\CustomerGifts;
use Mtech\Sampling\Models\Gifts;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\OTP;
use Mtech\Sampling\Models\Setting;
use Mtech\Sampling\Models\UserLocations;
use Mtech\Sampling\Models\SettingOTP;
use Mtech\Sampling\Models\ConfigApp;
use RainLab\User\Models\User As UserModel;
use Mtech\API\Transformers\GiftTransformer;
use Mtech\API\Transformers\CustomerTransformer;
use Mtech\API\Transformers\SettingOTPTransformer;
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
    protected $otpRepository;
    protected $configAppRepository;

    public function __construct(Customers $customer, CustomerGifts $customerGift, Gifts $gift, Projects $project, Locations $location, OTP $otp, ConfigApp $configApp) {
        $this->customerRepository = $customer;
        $this->customerGiftRepository = $customerGift;
        $this->giftRepository = $gift;
        $this->projectRepository = $project;
        $this->locationRepository = $location;
        $this->otpRepository = $otp;
        $this->configAppRepository = $configApp;
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/customer/store",
     *   description="Store Customer",
     *   summary="Store Customer",
     *   produces={"application/json"},
     *   tags={"Customer"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Store Customer",
     *     required=true,
     *    @SWG\Schema(example={
     *         "name": "Nguyễn Văn A",
     *         "gender":1,
     *         "dob":"05-02-1991",
     *         "phone":"0903123456",
     *         "cmnd":"0258963214",
     *         "address":"01 Lê Duẩn",
     *         "brand_in_use":"Unilever",
     *         "product":"Dầu gội",
     *         "product_sampling":1,
     *         "location_id":1,
     *         "otp":"123456789",
     *      })
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
            $otp = $request->get('otp');
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            $timeExpiredOTP = Setting::get('time_expire_otp');
            $checkValidOTP = $this->otpRepository
                            ->where('phone', $phone)
                            ->where('otp', $otp)
                            ->where('user_id', $userId)->first();

            $location = Locations::find($locationId);
            $projectID = $location->project->id;
            $configProject = $this->configAppRepository->where('project_id', $projectID)->first();
            $showOTP = $configProject->show_customer_otp;
            if ($showOTP) {
                if (!$checkValidOTP) {
                    return $this->respondWithError('Mã OTP không hợp lệ!', self::HTTP_BAD_REQUEST);
                }
                if ($checkValidOTP) {
                    $createdDate = $checkValidOTP->created_at;
                    $resultDate = date_diff(date_create($now), date_create($createdDate));
                    $resultDate = $resultDate->format("%i");
                    $resultDate = $resultDate * 60;
                    if ($resultDate > $timeExpiredOTP) {
                        return $this->respondWithError('Mã OTP đã hết hạn, Vui lòng đăng ký lại dịch vụ!', self::HTTP_BAD_REQUEST);
                    }
                }
            }
            if (!$location) {
                return $this->respondWithError('Data không hợp lệ!', self::HTTP_BAD_REQUEST);
            }
            $userReceiveGift = $location->project->user_receive_gift;
            $customer = $this->customerRepository->where('phone', $phone)->where('location_id', $locationId)->get();
            if (count($customer) >= $userReceiveGift) {
                return $this->respondWithError('Số điện thoại này đã nhận quà từ chương trình', self::HTTP_BAD_REQUEST);
            }

            //Store Customer
            $arrCustomer = ['name' => $name, 'cmnd' => $cmnd, 'dob' => $dob,
                'gender' => $gender, 'phone' => $phone, 'address' => $address, 'otp' => $otp,
                'brand_in_use' => $brandInUse, 'product_name' => $product, 'product_sampling' => $productSampling,
                'location_id' => $locationId, 'file_name_avatar' => '', 'file_name_bill' => '', 'created_at' => $now];
            $customerInfo = $this->customerRepository->create($arrCustomer);

            //Delete OTP
            if($showOTP){
                Db::table('mtech_sampling_otp')->where('id', $checkValidOTP->id)->delete();
            }
            $results = fractal($customerInfo, new CustomerTransformer())->toArray();
            return $this->respondWithSuccess($results, ('Store Customer successful!'));
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/customer/update-bill",
     *   description="Update Bill",
     *   summary="Store Customer",
     *   produces={"application/json"},
     *   tags={"Customer"},
     *   @SWG\Parameter(
     *         name="customer_id",
     *         in="formData",     
     *         required=true,
     *         type="string",
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
    public function updateBill(Request $request) {
        try {
            $now = date('Y-m-d H:i:s');
            $billImage = $request->file('bill_image');
            $customerID = $request->get('customer_id');

            //Get Info Customer
            $customer = $this->customerRepository->find($customerID);
            if ($billImage->isValid()) {
                $now = date('d-m-Y');
                $phone = $customer->phone;
                $name = $customer->name;
                $locationId = $customer->location_id;
                $locationName = HelperClass::getAlias($customer->location->location_name);
                $projectId = $customer->location->project_id;
                $projectName = HelperClass::getAlias($customer->location->project->project_name);
                $prefixName = $phone . "_" . $name;
                $fileName = HelperClass::convert_vi_to_en($prefixName);
                $fileName = preg_replace('/\s+/', '_', $fileName);
                $destinationPath = storage_path('app/media/' . $projectName . '_' . $projectId . '/' . $locationName . '_' . $locationId . '/' . $now . '/');
                $filePath = $projectName . '_' . $projectId . '/' . $locationName . '_' . $locationId . '/' . $now . '/';
                $fileNameBill = $filePath . $fileName . "_bill.png";
                $billImage->move($destinationPath, $fileNameBill);
            }
            //Update Customer            
            $customer->file_name_bill = $fileNameBill;
            $customer->save();
            $results = fractal($customer, new CustomerTransformer())->toArray();
            return $this->respondWithSuccess($results, ('Update Bill successful!'));
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/customer/update-avatar",
     *   description="Update Avatar Customer",
     *   summary="Update Avatar Customer",
     *   produces={"application/json"},
     *   tags={"Customer"},
     *   @SWG\Parameter(
     *         name="customer_id",
     *         in="formData",     
     *         required=true,
     *         type="string",
     *   ),
     *   @SWG\Parameter(
     *         name="customer_avatar",
     *         in="formData",
     *         description="Customer Avatar",
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
    public function updateAvatar(Request $request) {
        try {
            $now = date('Y-m-d H:i:s');
            $customerAvatar = $request->file('customer_avatar');
            $customerID = $request->get('customer_id');

            //Get Info Customer
            $customer = $this->customerRepository->find($customerID);
            if ($customerAvatar->isValid()) {
                $now = date('d-m-Y');
                $phone = $customer->phone;
                $name = $customer->name;
                $locationId = $customer->location_id;
                $locationName = HelperClass::getAlias($customer->location->location_name);
                $projectId = $customer->location->project_id;
                $projectName = HelperClass::getAlias($customer->location->project->project_name);
                $prefixName = $phone . "_" . $name;
                $fileName = HelperClass::convert_vi_to_en($prefixName);
                $fileName = preg_replace('/\s+/', '_', $fileName);
                $destinationPath = storage_path('app/media/' . $projectName . '_' . $projectId . '/' . $locationName . '_' . $locationId . '/' . $now . '/');
                $filePath = $projectName . '_' . $projectId . '/' . $locationName . '_' . $locationId . '/' . $now . '/';
                $fileNameAvatar = $filePath . $fileName . "_avatar.png";
                $customerAvatar->move($destinationPath, $fileNameAvatar);
            }
            //Update Customer            
            $customer->file_name_avatar = $fileNameAvatar;
            $customer->save();
            $results = fractal($customer, new CustomerTransformer())->toArray();
            return $this->respondWithSuccess($results, ('Update Avatar Customer successful!'));
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/customer/check-phone",
     *   description="",
     *   summary="Check Phone Customer",
     *   operationId="api.v1.checkPhone",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Check Phone Customer",
     *     required=true,
     *    @SWG\Schema(example={
     *         "phone": "091456987",
     *         "location_id": 1
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *   security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function checkPhone(Request $request) {
        try {
            $phone = $request->get('phone');
            $location = $request->get('location_id');
            $customer = $this->customerRepository->where('phone', $phone)->where('location_id', $location)->first();
            if ($customer) {
                return $this->respondWithError('Account existing. Please try again!', self::HTTP_BAD_REQUEST);
            }
            return $this->respondWithMessage("Account is valid!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\POST(
     *   path="/api/v1/customer/get-otp",
     *   description="",
     *   summary="Get OTP Customer",
     *   operationId="api.v1.getOTP",
     *   produces={"application/json"},
     *   tags={"Customer"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Check Phone Customer",
     *     required=true,
     *    @SWG\Schema(example={
     *         "phone": "0916214433",
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *   security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function getOTP(Request $request) {
        try {
            $now = date("Y-m-d H:i:s");
            $phone = $request->get('phone');
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            $locations = UserLocations::where('user_id', $userId)->get();
            $projectID = 0;
            foreach ($locations as $location) {
                $locationID = $location->location_id;
                $locationData = Locations::find($locationID);
                $projectStatus = $locationData->project->status;
                if ($projectStatus)
                    $projectID = $locationData->project_id;
            }
            $settingOTP = SettingOTP::where('project_id', $projectID)->first();
            $lengthOTP = $settingOTP->length_otp;
            $otpString = HelperClass::generateOTP($lengthOTP);
            $customer = $this->customerRepository->where('phone', $phone)->where('otp', $otpString)->first();
            if ($customer) {
                return $this->respondWithError('Số điện thoại này đã nhận quà từ chương trình', self::HTTP_BAD_REQUEST);
            }

            //Store Customer OTP
            $arrCustomerOTP = ['otp' => $otpString, 'user_id' => $userId, 'phone' => $phone, 'created_at' => $now];
            $otpCustomer = $this->otpRepository->create($arrCustomerOTP);

            //Get OTP BY Project
            $projectId = 0;
            $locations = UserLocations::where('user_id', $userId)->get();
            if (!$locations) {
                return false;
            }
            foreach ($locations as $location) {
                $locationData = Locations::find($location->location_id);
                $projectId = $locationData->project->id;
            }
            $result = $this->callSMSFibo($projectId, $otpCustomer->id, $phone, $otpString);
            $xml = simplexml_load_string($result);
            $data = json_decode(json_encode($xml), 1);
            if ($data) {
                $setting = SettingOTP::where('project_id', $projectId)->first();
                $results = fractal($setting, new SettingOTPTransformer())->toArray();
                return $this->respondWithSuccess($results, ('OTP has been send successfully!'));
            }
            return $this->respondWithMessage("OTP is valid!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    private function callSMSFibo($projectId, $otpID, $phone, $otpString) {
        $setting = SettingOTP::where('project_id', $projectId)->first();
        $brandName = $setting->brand_name;
        $accountName = $setting->account_name;
        $accountPassword = $setting->account_password;
        $textSMS = $setting->text_sms;
        /* $data = '{
          "submission":
          {
          "api_key":"' . $accountName . '",
          "api_secret":"' . $accountPassword . '",
          "sms": [
          {
          "id":"' . $otpID . '",
          "brandname":"' . $brandName . '",
          "text":"' . $textSMS . ':' . $otpString . '",
          "to":"' . $phone . '"
          }
          ]
          }
          }'; */
        $message = $otpString . " " . $textSMS;
        $url = $setting->url_telco;
        $url .= "?clientNo=" . $accountName . "&clientPass=" . $accountPassword . "&senderName=" . $brandName;
        $url .= "&phoneNumber=" . $phone;
        $url .= "&smsMessage=" . urlencode($message);
        $url .= "&smsGUID=0";
        $url .= "&serviceType=0";
        // Khởi tạo CURL
        $curl = curl_init($url);

        // Thiết lập có return
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        curl_close($curl);
        return $result;
    }

}
