<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiTrait{

    private function successResponse($data, $code = 200 ){
        return response()->json(['result' => $data, 'code' => $code], $code);
    }

    private function errorResponse($errors, $code){
        return response()->json(['error' => true, 'message' => $errors, 'code' => $code ], $code);
    }
}

?>