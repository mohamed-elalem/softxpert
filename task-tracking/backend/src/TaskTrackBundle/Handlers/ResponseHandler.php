<?php

namespace TaskTrackBundle\Handlers;

use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\SerializationHandler;
use Symfony\Component\HttpFoundation\Response;


class ResponseHandler {
    
    private static $serializer = null;
    
    /**
     * 
     * This method is responsible for creating a response object
     * 
     * @param type $response
     * @param type $status
     * @param type $extra Extra info other than code and message
     */
    
    public static function handle($code, $extra = [], $errorCode = null, $message = null) {
        $serializer = new SerializationHandler();
        $response = new Response();
        
        
        /**
         * Setting response header to application/json
         */
        $response->headers->set("Content-Type", "application/json");
        
        
        /**
         * Filling response body with necessary data
         */
        
        $responseBody = [];
        $responseBody["code"] = (is_null($code) ? Status::STATUS_SUCCESS : $code);
        if($responseBody["code"] != Status::STATUS_SUCCESS) {
            $responseBody["err_code"] = $errorCode;
            $responseBody["err_message"] = (! is_null($message) ? $message : (! is_null($errorCode) ? Status::MESSAGES[$errorCode] : ""));
        }
        if(count($extra)) {
            $responseBody["data"] = static::fillData($extra);
        }
        if($errorCode) {
            $response->setStatusCode(Status::RESPONSE_CODES[$errorCode]);
        }
        $content = self::$serializer->serialize($responseBody, "json");
        $response->setContent($content);
        return $response;
    }
    
    public static function setSerializer($serializer) {
        self::$serializer = $serializer;
    }
    
    private static function fillData($extra) {
        $data = [];
        foreach($extra as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
        
    }
    
}
