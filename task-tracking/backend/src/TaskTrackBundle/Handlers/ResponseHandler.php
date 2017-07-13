<?php

namespace TaskTrackBundle\Handlers;

use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\SerializationHandler;

class ResponseHandler {
    
//    private static $handler = null;
//    private $serializer;
//    
//    public static function getInstance() {
//        if(is_null(static::$handler)) {
//            static::$handler = new ResponseHandler();
//        }
//        return static::$handler;
//    }
//    
//    public function __construct() {
//        $this->serializer = new SerializationHandler();
//    }
//    
//    public function handle($status, $format, $extra = []) {
//        $response = [
//            "code" => $status,
//            "message" => Status::MESSAGES[$status]
//        ];
//        if(count($extra)) {
//            $response['extra'] = [];
//            foreach($extra as $key => $message) {
//                $response['extra'][$key] = $message;
//            }
//        }
//        return $this->serializer->serialize($response, $format);
//    }
    
    /**
     * 
     * This method is responsible for creating a response object
     * 
     * @param type $response
     * @param type $status
     * @param type $extra Extra info other than code and message
     */
    
    public static function handle($response, $extra = [], $status = null, $message = null) {
        $response->headers->set("Content-Type", "application/json");
        $serializer = new SerializationHandler();
        /**
         * Filling response body with necessary data
         */
        
        $responseBody = [];
        if(is_null($status)) {
            $responseBody["code"] = Status::SUCCESS;
        }
        else {
            $responseBody["code"] = $status;    
        }
        
        $status = $responseBody["code"];
        
        if(is_null($message)) {
            $responseBody["message"] = Status::MESSAGES[$status];
        }
        else {
            $responseBody["message"] = $message;        
        }
        if(count($extra)) {
            foreach($extra as $key => $value) {
                $responseBody["extra"][$key] = $value;
            }
        }
        $content = $serializer->serialize($responseBody, "json");
        
        $response->setContent($content);
        return $response;
    }
}
