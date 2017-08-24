<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;

class AuthenticationFailureListener {

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {

        $response = ResponseHandler::handle(Status::STATUS_FAILURE, [], Status::ERR_INVALID_CREDINTIALS);

        $event->setResponse($response);
    }

}