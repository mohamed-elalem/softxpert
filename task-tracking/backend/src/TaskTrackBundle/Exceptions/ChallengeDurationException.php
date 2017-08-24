<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Exceptions;
use TaskTrackBundle\Constants\Status;
/**
 * Description of ChallengeDurationException
 *
 * @author mohamedelalem
 */
class ChallengeDurationException extends FormValidationException {
    
    
    public function __construct($erroMessage = null) {
        parent::__construct($errorMessage);
    }
}
