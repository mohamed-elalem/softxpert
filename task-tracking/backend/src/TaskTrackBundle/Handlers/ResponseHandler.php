<?php

namespace TaskTrackBundle\Handlers;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use TaskTrackBundle\Constants\Status;

class ResponseHandler {
    
    private static $handler = null;
    private $serializer;
    
    public function __construct() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }
    
    public static function getInstance() {
        if(is_null(static::$handler)) {
            static::$handler = new ResponseHandler();
        }
        return static::$handler;
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
