<?php

namespace TaskTrackBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use \Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use TaskTrackBundle\Handlers\ResponseHandler;

class ExceptionListener {

    

    public function onKernelException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        $event->setResponse(ResponseHandler::handle($exception->getCode(), ["message" => $exception->getMessage()]));
    }

    public static function getSubscribedEvents(): array {
        return [
            "kernel.exception" => 'onKernelException'
        ];
    }

}
