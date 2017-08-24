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
class ItemNotFoundException extends GenericException {
    
    public function __construct($erroMessage = null, $extra = []) {
        parent::__construct(Status::RESOURCE_NOT_FOUND_EXCEPTION, $errorMessage, $extra);
    }
}
