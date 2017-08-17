<?php

namespace TaskTrackBundle\Constants;
class Status {
    
    /**
     * Status Codes
     */
    
    const ERR_USER_EXIST = 100;
    const ERR_ACTION_FORBIDDEN = 101;
    const ERR_TASK_EXIST = 102;
    const ERR_SERVER_ERROR = 103;
    const ERR_CHALLENGE_OWNER = 104;
    const ERR_MISSING_TASKS = 105;
    const ERR_INVALID_CHALLENGES_STRUCTURE = 106;
    const ERR_FORM_VALIDATION_ERROR = 107;
    
    
    
    
    /**
     * Exception Codes
     */
    
    const ERR_CHALLENGE_DURATION_VALIDATION_ERROR = 1001;
    
    /**
     * Return Codes
     */
    
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 0;
    
    const MESSAGES = [
        self::STATUS_SUCCESS => "Response Ok. no errors found.",
        self::ERR_USER_EXIST => "This account is already Registered.",
        self::ERR_ACTION_FORBIDDEN => "This is action is forbidden to your current status.",
        self::ERR_TASK_EXIST => "This task already exists.",
        self::ERR_SERVER_ERROR => "Server error. Please try again later.",
        self::ERR_CHALLENGE_OWNER => "You're not the owner of this action please select an owned one.",
        self::ERR_MISSING_TASKS => "You didn't add this task's prerequisites please add them before attempting this one.",
        self::ERR_INVALID_CHALLENGES_STRUCTURE => "Invalid challenge structures that contains cycles",
        self::ERR_FORM_VALIDATION_ERROR => "Data that you entered is not valid.",
    ];
    
    /**
     * Exception types
     */
    
    const ACCESS_DENIED_HTTP_EXCEPTION = 1003;
    const NOT_FOUND_HTTP_EXCEPTION = 1004;
    const SERVICE_NOT_FOUND_EXCEPTION = 1005;
    const SCORE_RANGE_ERROR_EXCEPTION = 1006;
    const CUSTOM_THROWN_EXCEPTION = 1000;
    
    
    const RESPONSE_CODES = [
        /**
         * Return Codes
         */
        
        self::STATUS_SUCCESS => 200,
        
        /**
         * Status Codes
         */
        
        self::ERR_USER_EXIST => 406,
        self::ERR_ACTION_FORBIDDEN => 403,
        self::ERR_TASK_EXIST => 406,
        self::ERR_SERVER_ERROR => 500,
        self::ERR_CHALLENGE_OWNER => 403,
        self::ERR_MISSING_TASKS => 406,
        self::ERR_INVALID_CHALLENGES_STRUCTURE => 406,
        self::ERR_FORM_VALIDATION_ERROR => 406,
        
        /**
         * Exception Codes
         */
        
        self::ERR_CHALLENGE_DURATION_VALIDATION_ERROR => 406,
        self::ACCESS_DENIED_HTTP_EXCEPTION => 500,
        self::NOT_FOUND_HTTP_EXCEPTION => 500,
        self::SERVICE_NOT_FOUND_EXCEPTION => 500,
        self::SCORE_RANGE_ERROR_EXCEPTION => 406,
    ];
    
}

