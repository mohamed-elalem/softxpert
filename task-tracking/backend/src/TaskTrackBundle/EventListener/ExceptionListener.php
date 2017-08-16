<?php

namespace TaskTrackBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use \Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;

class ExceptionListener {

    

    public function onKernelException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        $code = $exception->getCode();
        if($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            $code = Status::ACCESS_DENIED_HTTP_EXCEPTION;
        }
        else if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $code = Status::NOT_FOUND_HTTP_EXCEPTION;
        }
        else if($exception instanceof \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException) {
            $code = Status::SERVICE_NOT_FOUND_EXCEPTION;
        }
        
        $event->setResponse(ResponseHandler::handle(Status::STATUS_FAILURE, [], $code, $exception->getMessage()));
    }

    public static function getSubscribedEvents(): array {
        return [
            "kernel.exception" => 'onKernelException'
        ];
    }

}
