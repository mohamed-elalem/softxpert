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
    
    /**
     * Return Codes
     */
    
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 0;
    
    const MESSAGES = [
        1 => "Response Ok. no errors found",
        100 => "This account is already Registered",
        101 => "This is action is forbidden to your current status",
        102 => "This task already exists",
        103 => "Server error. Please try again later",
        104 => "You're not the owner of this action please select an owned one"
    ];
    
    const RESPONSE_CODES = [
        1 => 200,
        100 => 406,
        101 => 403,
        102 => 406,
        103 => 500,
        104 => 403,
    ];
}

