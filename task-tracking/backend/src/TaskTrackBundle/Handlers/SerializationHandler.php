<?php

namespace TaskTrackBundle\Handlers;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SerializationHandler {
    
    private $serializer;
    
    public function __construct() {
        $xmlEncoder = new XmlEncoder();
        $jsonEncoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
//        $normalizer->setIgnoredAttributes(["user_id", "challenge_id"]);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object;
        });
        
        $this->serializer = new Serializer([$normalizer], [$xmlEncoder, $jsonEncoder]);
    }
    
    public function serialize($data, $format) {
        $encodedData = $this->serializer->serialize($data, $format);
        return $encodedData;
    }
    
    public function deserialize($encode, $type, $format) {
        $data = $this->serializer->des($encode, $type, $format);
        return $data;
    }
}

