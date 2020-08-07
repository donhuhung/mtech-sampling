<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\ConfigApp;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\ProductSampling;
use Mtech\Sampling\Models\Locations;
use Mtech\API\Transformers\ProductSamplingTransformer;
use Mtech\API\Transformers\SettingTransformer;
use Mtech\API\Transformers\LocationTransformer;
use Mtech\API\Transformers\ProjectTransformer;
use Mtech\Sampling\Models\UserLocations;
use JWTAuth;

/**
 * Setting Back-end Controller
 */
class Setting extends General {

    protected $settingRepository;
    protected $projectRepository;
    protected $productSamplingRepository;
    protected $locationRepository;

    public function __construct(ConfigApp $configApp, Projects $project, ProductSampling $productSampling, Locations $location) {
        $this->settingRepository = $configApp;
        $this->projectRepository = $project;
        $this->productSamplingRepository = $productSampling;
        $this->locationRepository = $location;
        $this->projectRepository = $project;
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/config/app",
     *   description="",
     *   summary="Congif App",
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
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            $locations = UserLocations::where('user_id', $userId)->get();
            if (!$locations) {
                return $this->respondWithMessage('Data not found!');
            }
            if ($locations) {
                foreach ($locations as $location) {
                    $locationData = Locations::find($location->location_id);
                    $projectId = $locationData->project->id;
                }                
            }            
            $setting = $this->settingRepository->where('project_id', $projectId)->first();
            $results = fractal($setting, new SettingTransformer())->toArray();
            return $this->respondWithSuccess($results, "Get Config succesful!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/v1/general/get-product-sampling",
     *   description="",
     *   summary="Get List Product Sampling",
     *   operationId="api.v1.getProductSampling",
     *   produces={"application/json"},
     *   tags={"General"},
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function getProductSampling(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;
            $locations = UserLocations::where('user_id', $userId)->get();
            $projectId = 0;
            if ($locations) {
                foreach ($locations as $location) {
                    $locationData = Locations::find($location->location_id);                    
                    $projectStatus = $locationData->project->status;
                    if($projectStatus)
                        $projectId = $locationData->project->id;
                }
            }
            $products = $this->productSamplingRepository->where('status',1)->where('project_id',$projectId)->get();
            if (!$products) {
                return $this->respondWithMessage('Data not found!');
            }
            $results = fractal($products, new ProductSamplingTransformer())->toArray();
            return $this->respondWithSuccess($results, "Get List Product Sampling succesful!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/v1/general/get-list-location",
     *   description="",
     *   summary="Get List Location",
     *   operationId="api.v1.getListLocation",
     *   produces={"application/json"},
     *   tags={"General"},
     * @SWG\Response(response=200, description="Server is OK!"),
     * @SWG\Response(response=500, description="Internal server error!"),
     *  security={
     *     {"bearerAuth":{}}
     *   }
     * )
     */
    public function getLocations() {
        try {
            $projects = $this->projectRepository->where('status', 1)->get();
            if (!$projects) {
                return $this->respondWithMessage('Data not found!');
            }
            $arrProject = [];
            foreach ($projects as $project) {
                array_push($arrProject, $project->id);
            }
            $locations = $this->locationRepository->whereIn('project_id', $arrProject)->get();
            if (!$locations) {
                return $this->respondWithMessage('Data not found!');
            }
            $results = fractal($locations, new LocationTransformer())->toArray();
            return $this->respondWithSuccess($results, "Get List Location succesful!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/v1/general/get-list-project",
     *   description="",
     *   summary="Get List Project",
     *   operationId="api.v1.getListProject",
     *   produces={"application/json"},
     *   tags={"General"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Get List Project",
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
    public function getProjects(Request $request) {
        try {
            $locationId = $request->get('location_id');
            $location = $this->locationRepository->find($locationId);
            $projectId = $location->project_id;
            $project = $this->projectRepository->where('status', 1)->where('id', $projectId)->first();
            if (!$project) {
                return $this->respondWithMessage('Data not found!');
            }
            $results = fractal($project, new ProjectTransformer())->toArray();
            return $this->respondWithSuccess($results, "Get List Project succesful!");
        } catch (\Exception $ex) {
            return $this->respondWithError($ex->getMessage(), self::HTTP_BAD_REQUEST);
        }
    }

}
