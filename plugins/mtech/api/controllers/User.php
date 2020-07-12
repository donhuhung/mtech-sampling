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
            $credentials = $request->only('email', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->respondWithError('Email or password incorrect', self::HTTP_INTERNAL_SERVER_ERROR);
            }
            $user = $this->userRepository->where('email', $email)->first();
            if ($user) {
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
                        $user->token = JWTAuth::fromUser($user);
                        $token = JWTAuth::fromUser($user);
                    }
                    $results['data']['access_token'] = $token;
                    return $this->respondWithSuccess($results, "Login succesful!");                    
                } else {
                    return $this->respondWithError('Username or password incorrect', self::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return $this->respondWithError('Username or password incorrect', self::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout() {
        try {
            JWTAuth::invalidate();
            return $this->respondWithMessage('Đăng xuất thành công');
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }
    

}
