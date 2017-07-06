<?php

namespace TaskTrackBundle\Handlers;

use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\SerializationHandler;

class ResponseHandler {
    
    private static $handler = null;
    private $serializer;
    
    public static function getInstance() {
        if(is_null(static::$handler)) {
            static::$handler = new ResponseHandler();
        }
        return static::$handler;
    }
    
    public function __construct() {
        $this->serializer = new SerializationHandler();
    }
    
    public function handle($status, $format, $extra = []) {
        $response = [
            "code" => $status,
            "message" => Status::MESSAGES[$status]
        ];
        
        foreach($extra as $key => $message) {
            $response[] = [$key => $message];
        }
        return $this->serializer->serialize($response, $format);
    }
}
