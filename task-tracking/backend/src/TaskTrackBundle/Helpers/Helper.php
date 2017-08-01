<?php

namespace TaskTrackBundle\Helpers;

class Helper {

    public function getRepository($controller, $name) {
        return $controller->getDoctrine()->getRepository($name);
    }    
}


