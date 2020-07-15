<?php namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Validator;
use Hash;
use Mtech\Sampling\Models\ConfigApp;
use Mtech\Sampling\Models\Projects;
use Mtech\API\Transformers\UserTransformer;
use Lang;
use JWTAuth;

/**
 * Setting Back-end Controller
 */
class Setting extends General
{
    protected $settingRepository;
    protected $projectRepository;
    public function __construct(ConfigApp $configApp, Projects $project)
    {
        $this->settingRepository = $configApp;
        $this->projectRepository = $project;
    }
    
    /**
     * @SWG\Post(
     *   path="/api/v1/config/app",
     *   description="",
     *   summary="Login User",
     *   operationId="api.v1.configApp",
     *   produces={"application/json"},
     *   tags={"Setting"},
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function configApp(Request $request) {
        try {
            $now = date('Y-m-d H:i:s');
            $project = $this->projectRepository->where('status',1)->first();
            if(!$project){
                return $this->respondWithMessage('Data not found!');
            }
            $projectId = $project->id;
            echo $projectId;die;
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
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }
}
