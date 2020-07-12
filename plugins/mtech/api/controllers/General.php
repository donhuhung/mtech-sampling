<?php namespace Mtech\API\Controllers;

use \Illuminate\Routing\Controller;
use RainLab\User\Models\User AS UserModel;
use RainLab\User\Models\UserGroup;
use Illuminate\Http\Response;

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Focus Sampling",
 *         description="Focus Sampling",
 *     ),
 *      @SWG\SecurityScheme(
 *          securityDefinition="bearerAuth",
 *          description="Enter Bearer {{access_token}}",
 *          type="apiKey",
 *          in="header",
 *          name="Authorization"
 *      )
 * )
 */

/**
 * General Back-end Controller
 */
class General extends Controller {

    protected $statusCode = Response::HTTP_OK;

    const HTTP_NOT_FOUND = Response::HTTP_NOT_FOUND;
    const HTTP_INTERNAL_SERVER_ERROR = Response::HTTP_INTERNAL_SERVER_ERROR;
    const HTTP_BAD_REQUEST = Response::HTTP_BAD_REQUEST;
    const HTTP_UNAUTHORIZED = Response::HTTP_UNAUTHORIZED;
    const HTTP_METHOD_NOT_ALLOWED = Response::HTTP_METHOD_NOT_ALLOWED;

    public function __construct() {

    }

    protected function getStatusCode() {
        return $this->statusCode;
    }

    protected function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    protected function respondWithError($message = null, $statusCode = null) {
        if (is_null($statusCode)) {
            $this->setStatusCode(200);
        } else {
            $this->setStatusCode($statusCode);
        }
        $response = [
            'status' => true,
            'status_code' => $this->getStatusCode(),
            'message' => $message,
        ];

        return $this->respondWithArray($response);
    }

    protected function respondWithArray(array $array, array $headers = []) {
        return response()->json($array, $this->statusCode, $headers);
    }

    protected function respondWithSuccess($data = [],$message = 'Action Successfully') {
        if(isset($data['data'])){
            $data = $data['data'];
        }
        $response = [
            'status' => false,
            'status_code' => $this->getStatusCode(),
            'message' => $message,
            'data' => $data,
        ];

        return $this->respondWithArray($response);
    }

    protected function respondWithData($data = [], array $headers = []) {
        $array = array_merge([
            'status' => false,
            'status_code' => $this->getStatusCode(),
            'data' => $data,
        ]);
        return $this->respondWithArray($array, $headers);
    }

    protected function respondWithDataPaging($data = [], $pagination = []) {
        $array = array_merge([
            'status' => false,
            'status_code' => $this->getStatusCode(),
            'data' => $data,
            'paging' => $pagination
        ]);
        return $this->respondWithArray($array);
    }

    protected function respondWithMessage($message, array $headers = []) {
        $array = array_merge([
            'status' => false,
            'status_code' => $this->getStatusCode(),
            'message' => $message,
        ]);
        return $this->respondWithArray($array, $headers);
    }

    protected function checkAuthUser($request) {
        $user = $request->user();
        if (!$user) {
            return $this->respondWithError('user is invalid', 404);
        }
    }

    /**
     * random String Password
     *
     * @return \Illuminate\Http\Response
     */
    public static function randomString($length = 10) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function getRandomCode($length = 6) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
