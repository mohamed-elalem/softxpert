<?php

namespace SoftXpert\LoginBundle\Constants;

class Status {
    const SUCCESS = 0;
    const FAILURE = 1;
    
    
    const MESSAGES = [
        SUCCESS => "Response Ok.",
        FAILURE => "Request failed to send"
    ];
}