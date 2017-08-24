<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Exceptions;
use TaskTrackBundle\Constants\Status;
/**
 * Description of MissingDependenciesException
 *
 * @author mohamedelalem
 */
class MissingDependenciesException extends GenericException {

    public function __construct($errorMessage = null, $extra = []) {
        parent::__construct(Status::ERR_MISSING_TASKS, $errorMessage, $extra);
    }
    
}
