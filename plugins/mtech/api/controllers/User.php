<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Validator;
use Hash;
use RainLab\User\Models\User As UserModel;
use Rainlab\User\Models\UserGroup;
use Mtech\API\Transformers\UserTransformer;
use Lang;
use JWTAuth;
use Mtech\Sampling\Models\HistoryPG;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\UserLocations;
use Mtech\API\Classes\HelperClass;

/**
 * User Back-end Controller
 */
class User extends General {

    protected $userRepository;

    public function __construct(UserModel $user) {
        $this->userRepository = $user;
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/login",
     *   description="Login User",
     *   summary="Login User",
     *   operationId="api.v1.postLoginUser",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Login User",
     *     required=true,
     *    @SWG\Schema(example={
     *         "email": "test@gmail.com",
     *         "password": "123456789",
     *         "longitude": "132121",
     *         "latitude": "4343434",
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     * )
     */
    public function login(Request $request) {
        try {
            $now = date('Y-m-d H:i:s');
            $email = $request->get('email');
            $password = $request->get('password');
            $phone = $request->get('phone');
            $longitude = $request->get('longitude');
            $latitude = $request->get('latitude');
            if($phone){
                $credentials = $request->only('phone', 'password');
            }            
            else{
                $credentials = $request->only('email', 'password');
            }
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->respondWithError('Email or password incorrect', self::HTTP_INTERNAL_SERVER_ERROR);
            }
            $user = $this->userRepository->where('email', $email)->first();
            if (!$user) {
                $user = $this->userRepository->where('phone', $phone)->first();
                if (!$user)
                    return $this->respondWithError('Email/Phone or password incorrect', self::HTTP_BAD_REQUEST);
            }
            if ($user->is_activated == 0) {
                return $this->respondWithError('The account has not been activated. Please contact the admin', self::HTTP_INTERNAL_SERVER_ERROR);
            }
            if (Hash::check($password, $user->password)) {
                $userModel = JWTAuth::authenticate($token);
                $user->last_login = $now;
                $user->save();

                $results = fractal($user, new UserTransformer())->toArray();
                if ($userModel->methodExists('getAuthApiSigninAttributes')) {
                    $user = $userModel->getAuthApiSigninAttributes();
                } else {
                    $token = JWTAuth::fromUser($user);
                    $user->access_token = $token;
                }
                $results['data']['access_token'] = $token;
                $this->updateHistory($user, true, $latitude, $longitude);
                return $this->respondWithSuccess($results, "Login succesful!");
            } else {
                return $this->respondWithError('Username or password incorrect', self::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/logout",
     *   description="",
     *   summary="Logout User",
     *   operationId="api.v1.logout",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function logout(Request $request) {
        try {
            $token = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $token);
            $user = JWTAuth::authenticate($token);
            $this->updateHistory($user, false);
            JWTAuth::invalidate();
            return $this->respondWithMessage('Logout succesful!');
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/forgot-password",
     *   description="",
     *   summary="Login User",
     *   operationId="api.v1.forgotPassword",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Login User",
     *     required=true,
     *    @SWG\Schema(example={
     *         "email": "test@gmail.com"
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     * )
     */
    public function forgotPassWord(Request $request) {
        try {
            $email = $request->get('email');
            $phone = $request->get('phone');
            $user = $this->userRepository->where('email', $email)->first();
            if (!$user) {
                $user = $this->userRepository->where('phone', $phone)->first();
                if (!$user)
                    return $this->respondWithError('Account does not exist. Please try again!', self::HTTP_BAD_REQUEST);
            }
            $password = HelperClass::randomString(6);
            $user->password = $password;
            $user->password_confirmation = $password;
            $user->reset_password_code = $password;
            $user->change_password = 0;
            $user->is_activated = 0;
            $user->save();

            return $this->respondWithMessage("Reset Password succesfully!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/relogin",
     *   description="",
     *   summary="Login User",
     *   operationId="api.v1.reLogin",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Login User",
     *     required=true,
     *    @SWG\Schema(example={
     *         "email": "test@gmail.com",     
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     * )
     */
    public function reLogin(Request $request) {
        try {
            $now = date('Y-m-d H:i:s');
            $email = $request->get('email');
            $phone = $request->get('phone');
            $user = $this->userRepository->where('email', $email)->first();
            if (!$user) {
                $user = $this->userRepository->where('phone', $phone)->first();
                if (!$user)
                    return $this->respondWithError('Account does not exist. Please try again!', self::HTTP_BAD_REQUEST);
            }
            if ($user->is_activated == 0) {
                return $this->respondWithError('The account has not been approved. Please wait for administrator approval!', self::HTTP_INTERNAL_SERVER_ERROR);
            }
            $user->last_login = $now;
            $user->save();

            $results = fractal($user, new UserTransformer())->toArray();
            $user->token = JWTAuth::fromUser($user);
            $token = JWTAuth::fromUser($user);
            $results['data']['access_token'] = $token;
            return $this->respondWithSuccess($results, "Login succesful!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/change-password",
     *   description="",
     *   summary="Change Password",
     *   operationId="api.v1.changePassword",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Change Password",
     *     required=true,
     *    @SWG\Schema(example={
     *         "new_password": "123456789",
     *         "confirm_password": "123456789"
     *      })
     *   ),
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function changePassword(Request $request) {
        try {
            $newPassword = $request->get('new_password');
            $confirmPassword = $request->get('confirm_password');
            $userToken = JWTAuth::parseToken()->authenticate();
            $user = $this->userRepository->find($userToken->id);
            if ($newPassword != $confirmPassword) {
                return $this->respondWithError('Password confirmation does not match!', self::HTTP_BAD_REQUEST);
            }
            $user->password = $newPassword;
            $user->password_confirmation = $confirmPassword;
            $user->change_password = 1;
            $user->save();

            return $this->respondWithMessage("Change Password succesfully!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/checkin",
     *   description="",
     *   summary="User Checkin",
     *   operationId="api.v1.userCheckin",
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *         name="user_image",
     *         in="formData",
     *         description="User Image",
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
    public function userCheckin(Request $request) {
        try {
            $userImage = $request->file('user_image');
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            if ($userImage->isValid()) {
                $now = date('d-m-Y');
                $destinationPath = storage_path('app/media/' . $userId . '/' . $now . '/');
                $fileName = $userId . "_checkin.png";
                $userImage->move($destinationPath, $fileName);
                $historyPG = $this->checkHistoryPG($userId, true);
                $historyPG->checkin_image = $userId.'/'.$now.'/'.$fileName;
                $historyPG->save();
                return $this->respondWithMessage("Checkin succesfully!");
            }else{
                return $this->respondWithError('File Is Valid', self::HTTP_BAD_REQUEST);
            }            
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/user/checkout",
     *   description="",
     *   summary="User Checkout",
     *   operationId="api.v1.userCheckout",
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *         name="user_image",
     *         in="formData",
     *         description="User Image",
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
    public function userCheckout(Request $request) {
        try {
            $userImage = $request->file('user_image');
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            if ($userImage->isValid()) {
                $now = date('d-m-Y');
                $destinationPath = storage_path('app/media/' . $userId . '/' . $now . '/');
                $fileName = $userId . "_checkout.png";
                $userImage->move($destinationPath, $fileName);
                $historyPG = $this->checkHistoryPG($userId, true);
                $historyPG->checkout_image = $userId.'/'.$now.'/'.$fileName;
                $historyPG->save();
                return $this->respondWithMessage("Checkout succesfully!");
            }else{
                return $this->respondWithError('File Is Valid', self::HTTP_BAD_REQUEST);
            }            
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    protected function updateHistory($user, $is_login, $latitude = null, $longitude = null) {
        $userId = $user->id;
        $checkHistory = $this->checkHistoryPG($userId, $is_login);
        if (!$checkHistory) {
            $user_location_ids = UserLocations::where('user_id', '=', $userId)
                            ->groupBy('location_id')->select('location_id')->get()->pluck('location_id');
            $location_ids = Locations::join('mtech_sampling_projects', 'mtech_sampling_projects.id', '=', 'mtech_sampling_locations.project_id')
                            ->whereIn('mtech_sampling_locations.id', $user_location_ids)
                            ->where('mtech_sampling_projects.status', 1)->select('mtech_sampling_locations.id as id')->get()->pluck('id');
            if ($is_login) {
                $data = [];
                foreach ($location_ids as $location_id) {
                    $data[] = [
                        'user_id' => $userId,
                        'location_id' => $location_id,
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'login_time' => date('Y-m-d H:i:s')
                    ];
                }
                HistoryPG::insert($data);
                return 0;
            }
            $historyPgs = HistoryPG::where('user_id', $userId)->whereIn('location_id', $location_ids)
                            ->whereNull('logout_time')->get();
            foreach ($historyPgs as $historyPg) {
                $historyPg->logout_time = date('Y-m-d H:i:s');
                $historyPg->save();
            }
            return 1;
        }
    }

    protected function checkHistoryPG($userId, $isLogin) {
        $now = date('Y-m-d');
        $type = 'logout_time';
        if ($isLogin)
            $type = 'login_time';
        return HistoryPG::where('user_id', $userId)->whereDate($type, $now)->first();
    }

}
