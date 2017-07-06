<?php

namespace TaskTrackBundle\Handlers;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SerializationHandler {
    
    private $serializer;
    
    public function __construct() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
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

