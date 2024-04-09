<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function signResponse($msg,$user, $token,$status):JsonResponse
    {
        return response()->json([
            'message' => $msg,
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
            'status' => true,
            'code' => $status,
        ], $status);
    }
    public function errorResponse($msg):JsonResponse
    {
        return response()->json([
            'message' => $msg,
            'data' => [],
            'status' => false,
            'code' => Response::HTTP_NOT_FOUND,
        ], Response::HTTP_NOT_FOUND);
    }
}
