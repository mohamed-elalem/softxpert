<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\ResponseHandler;

/**
 * Description of JWTNotFoundListener
 *
 * @author mohamedelalem
 */
class JWTNotFoundListener {

/**
 * @param JWTNotFoundEvent $event
 */

    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        
        $response = ResponseHandler::handle(Status::STATUS_FAILURE, [], Status::ERR_JWT_NOT_FOUND);

        $event->setResponse($response);
    }
}
