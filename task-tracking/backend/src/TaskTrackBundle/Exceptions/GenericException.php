<?php

namespace TaskTrackBundle\Exceptions;
use Symfony\Component\Config\Definition\Exception\Exception;
use TaskTrackBundle\Constants\Status;

/**
 * Exception types....
 * ERR_USER_EXIST
 * ERR_ACTION_FORBIDDEN
 * ERR_TASK_EXIST
 * ERR_SERVER_ERROR
 * ERR_CHALLENGE_OWNER
 * ERR_MISSING_TASKS
 * ERR_INVALID_CHALLENGES_STRUCTURE
 * ERR_FORM_VALIDATION_ERROR
 * ERR_CHALLENGE_NOT_EXIST
 * ERR_TASK_NOT_EXIST
 * ERR_INVALID_CREDINTIALS
 * ERR_INVALID_JWT
 * ERR_JWT_NOT_FOUND
 * ERR_JWT_EXPIRED
 * ERR_ACCESS_DENIED
 * ERR_NOT_FOUND_HTTP
 * ERR_SERVICE_NOT_FOUND
 */

class GenericException extends Exception {
    
    private $extra;
    
    public function __construct($errorCode, $errorMessage = null, $extra = []) {
        parent::__construct($errorMessage, $errorCode);
        $this->extra = $extra;
    }
    
    public function getExtra() {
        return $this->extra;
    }
}
