<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

    public function sendResponse($result,$message,$total=0){

        if($total>0){
            $response=[
                'success' => 200,
                'message' => $message,
                'total' => $total,
                'data' => $result
            ];
        }else{
            $response=[
                'success' => 200,
                'message' => $message,
                'data' => $result
            ];
        }

        return response()->json($response, 200);

    }

    public function sendError($errorResponse,$errorMessage=[],$responseStatus=400){
        // return ($errorMessage);
        $response = [
            'error' => 400,
            'message' => $errorResponse
        ];

        if(!empty($errorMessage)){
            $response['data'] = $errorMessage;
        }

        return response()->json($response, $responseStatus);
    }
}
