<?php namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Validator;
use Hash;
use Mtech\Sampling\Models\ConfigApp;
use Mtech\Sampling\Models\Projects;
use Mtech\API\Transformers\SettingTransformer;
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
            $project = $this->projectRepository->where('status',1)->first();
            if(!$project){
                return $this->respondWithMessage('Data not found!');
            }
            $projectId = $project->id;            
            $setting = $this->settingRepository->where('project_id',$projectId)->first();
            $results = fractal($setting, new SettingTransformer())->toArray();
            return $this->respondWithSuccess($results, "Get Config succesful!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }
}
