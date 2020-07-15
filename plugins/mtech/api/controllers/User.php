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
     *   description="",
     *   summary="Login User",
     *   operationId="api.v1.postUpdateProfile",
     *   produces={"application/json"},
     *   tags={"User"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Login User",
     *     required=true,
     *    @SWG\Schema(example={
     *         "email": "test@gmail.com",
     *         "password": "12345678"
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
            $credentials = $request->only('email', 'password');
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
    public function logout() {
        try {
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
     *         "email": "test@gmail.com",
     *         "password": "12345678"
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
            $password = $request->get('password');
            $user = $this->userRepository->where('email', $email)->first();
            if (!$user) {
                $user = $this->userRepository->where('phone', $phone)->first();
                if (!$user)
                    return $this->respondWithError('Account does not exist. Please try again!', self::HTTP_BAD_REQUEST);
            }
            $user->password = $password;
            $user->password_confirmation = $password;
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

}
