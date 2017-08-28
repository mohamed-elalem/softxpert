<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Exceptions;
use TaskTrackBundle\Constants\Status;
/**
 * Description of ResourceNotFoundException
 *
 * @author mohamedelalem
 */
class ResourceNotFoundException extends GenericException {
    
    public function __construct($errorMessage = null, $extra = array()) {
        parent::__construct(Status::ERR_RESOURCE_NOT_FOUND, $errorMessage, $extra);
    }
}
