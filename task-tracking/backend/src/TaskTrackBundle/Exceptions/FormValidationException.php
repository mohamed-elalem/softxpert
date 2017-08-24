<?php

namespace TaskTrackBundle\Exceptions;
use TaskTrackBundle\Constants\Status;

class FormValidationException extends GenericException {
    
    public function __construct($errorMessage = null, $extra = []) {
        parent::__construct(Status::ERR_FORM_VALIDATION_ERROR, $errorMessage, $extra);
    }
}
