<?php

namespace TaskTrackBundle\Constants;

class Status {
    const SUCCESS = 0;
    const FAILURE = 1;
    const EXIST = 2;
    
    const MESSAGES = [
        0 => "Response Ok. no errors found",
        1 => "Request failed. errors were found",
        2 => "This email is already Registered",
    ];
}
