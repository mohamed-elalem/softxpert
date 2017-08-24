<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\ResponseHandler;

class JWTInvalidListener {

/**
 * @param JWTInvalidEvent $event
 */

    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $response = ResponseHandler::handle(Status::STATUS_FAILURE, [], Status::ERR_INVALID_JWT);

        $event->setResponse($response);
    }
}
