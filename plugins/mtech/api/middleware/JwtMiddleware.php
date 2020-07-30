<?php

namespace Mtech\API\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Illuminate\Http\Response;
use Mtech\Sampling\Models\ConfigApp;

class JwtMiddleware extends BaseMiddleware {

    protected $statusCode = Response::HTTP_OK;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {        
        $now = date('H:i:s');
        $conigApp = ConfigApp::first();        
        $timeNotLoginFrom = $conigApp->time_not_login_from;
        $timeNotLoginTo = $conigApp->time_not_login_to;
        if($timeNotLoginFrom <= $now && $now <= $timeNotLoginTo){
            return $this->respondWithError("Đã hết giờ làm việc. Vui lòng quay lại sau.", 304);
        }
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return $this->respondWithError("Account does not exist. Please try again!", Response::HTTP_BAD_REQUEST);
        }
        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->respondWithError("Token expired. Please login again!", $e->getStatusCode());
        } catch (JWTException $e) {
            return $this->respondWithError("Token expired. Please login again!", $e->getStatusCode());
        }
        if (! $user) {
            return $this->respondWithError("Account does not exist. Please try again!", 401);
        }
        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }

    protected function respondWithError($message = null, $statusCode = null) {
        $this->statusCode = $statusCode;
        $response = [
            'error' => true,
            'error_code' => $this->statusCode,
            'message' => $message,
        ];

        return $this->respondWithArray($response);
    }

    protected function respondWithArray(array $array, array $headers = []) {
        return response()->json($array, $this->statusCode, $headers);
    }
}
