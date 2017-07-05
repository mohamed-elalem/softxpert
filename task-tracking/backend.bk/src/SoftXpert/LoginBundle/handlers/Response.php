<?php

namespace SoftXpert\Handlers;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use SoftXpert\Constants\Status;

class ResponseHandler {
    
    private static $handler = null;
    private $serializer;
    
    public function __construct() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }
    
    public static function getInstance() {
        if(is_null($handler)) {
            static::$handler = new ResponseHandler();
        }
        return $this->handler;
    }
    
    public function handle($status, $format) {
        $response = [
            "code" => $status,
            "message" => Status::MESSAGES[$status]
        ];
        return $this->serializer->serialize($response, $format);
    }
}