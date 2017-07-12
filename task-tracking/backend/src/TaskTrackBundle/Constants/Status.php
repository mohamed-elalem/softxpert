<?php

namespace TaskTrackBundle\Constants;
class Status {
    const SUCCESS = 0;
    const FAILURE = 1;
    const EXIST = 2;
    const FORBIDDEN = 3;
    
    const MESSAGES = [
        0 => "Response Ok. no errors found",
        1 => "Request failed. errors were found",
        2 => "This email is already Registered",
        3 => "This is action is forbidden to your current status",
    ];
    
    const RESPONSE_CODES = [
        0 => 200,
        1 => 500,
        2 => 200,
        3 => 403,
    ];
}

