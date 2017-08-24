<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Exceptions;

/**
 * Description of ConnectionExistException
 *
 * @author mohamedelalem
 */
class ConnectionExistException extends FormValidationException {

    public function __construct($errorMessage = null) {
        parent::__construct($errorMessage);
    }
}
