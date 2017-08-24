<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\ResponseHandler;

/**
 * Description of JWTExpiredListener
 *
 * @author mohamedelalem
 */
class JWTExpiredListener {

    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $response = ResponseHandler::handle(Status::STATUS_FAILURE, [], Status::ERR_JWT_EXPIRED);
        
        $event->setResponse($response);
        
    }
}
