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
    const ERR_CHALLENGE_NOT_EXIST = 108;
    const ERR_TASK_NOT_EXIST = 109;
    const ERR_INVALID_CREDINTIALS = 110;
    const ERR_INVALID_JWT = 111;
    const ERR_JWT_NOT_FOUND = 112;
    const ERR_JWT_EXPIRED = 113;
    const ERR_RESOURCE_EXIST = 114;
    
    
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
        self::ERR_CHALLENGE_NOT_EXIST => "This challenge doesn't exist",
        self::ERR_TASK_NOT_EXIST => "This task doesn't exist",
        self::ERR_INVALID_CREDINTIALS => "The credintials provided doesn't match our records",
        self::ERR_INVALID_JWT => "Your token is invalid",
        self::ERR_JWT_NOT_FOUND => "Token is not found",
        self::ERR_JWT_EXPIRED => "Your token is expired",
        self::ERR_RESOURCE_NOT_FOUND => "Resource not found",
        self::ERR_ACTION_FORBIDDEN => "You're not authorized for this action",
        self::ERR_RESOURCE_EXIST => "Resource requested for creation already exist",
    ];
    
    /**
     * Exception types
     */
    
    const ERR_ACCESS_DENIED = 1003;
    const ERR_NOT_FOUND_HTTP = 1004;
    const ERR_SERVICE_NOT_FOUND = 1005;
    const ERR_RESOURCE_NOT_FOUND = 1006;
    
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
        self::ERR_CHALLENGE_NOT_EXIST => 404,
        self::ERR_TASK_NOT_EXIST => 404,
        self::ERR_JWT_NOT_FOUND => 404,
        self::ERR_JWT_EXPIRED => 401,
        self::ERR_INVALID_CREDINTIALS => 401,
        self::ERR_INVALID_JWT => 403,
        self::ERR_RESOURCE_EXIST => 406,
        
        /**
         * Exception Codes
         */
        
        self::ERR_CHALLENGE_DURATION_VALIDATION_ERROR => 406,
        self::ERR_ACCESS_DENIED => 500,
        self::ERR_NOT_FOUND_HTTP => 500,
        self::ERR_SERVICE_NOT_FOUND => 500,
        self::ERR_RESOURCE_NOT_FOUND => 404,
    ];
    
    /**
     * Lookup table for codes
     */
    
    const LOOKUP = [
        "ERR_USER_EXIST" => self::ERR_USER_EXIST,
        "ERR_ACTION_FORBIDDEN" => self::ERR_ACTION_FORBIDDEN,
        "ERR_TASK_EXIST" => self::ERR_TASK_EXIST,
        "ERR_SERVER_ERROR" => self::ERR_SERVER_ERROR,
        "ERR_CHALLENGE_OWNER" => self::ERR_CHALLENGE_OWNER,
        "ERR_MISSING_TASKS" => self::ERR_MISSING_TASKS,
        "ERR_INVALID_CHALLENGES_STRUCTURE" => self::ERR_INVALID_CHALLENGES_STRUCTURE,
        "ERR_FORM_VALIDATION_ERROR" => self::ERR_FORM_VALIDATION_ERROR,
        "ERR_CHALLENGE_NOT_EXIST" => self::ERR_CHALLENGE_NOT_EXIST,
        "ERR_TASK_NOT_EXIST" => self::ERR_TASK_NOT_EXIST,
        "ERR_INVALID_CREDINTIALS" => self::ERR_INVALID_CREDINTIALS,
        "ERR_INVALID_JWT" => self::ERR_INVALID_JWT,
        "ERR_JWT_NOT_FOUND" => self::ERR_JWT_NOT_FOUND,
        "ERR_JWT_EXPIRED" => self::ERR_JWT_EXPIRED,
        "ERR_ACCESS_DENIED" => self::ERR_ACCESS_DENIED,
        "ERR_NOT_FOUND_HTTP" => self::ERR_NOT_FOUND_HTTP,
        "ERR_SERVICE_NOT_FOUND" => self::ERR_SERVICE_NOT_FOUND,
    ];
    
}

