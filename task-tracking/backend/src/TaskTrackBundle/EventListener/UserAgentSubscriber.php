<?php

namespace TaskTrackBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use \Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserAgentSubscriber implements EventSubscriberInterface {

    private $em;
    private $tokenStorage;
    
    public function __construct(EntityManager $em, TokenStorage $tokenStorage) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function onKernelRequest(GetResponseEvent $event) {
        $token = $event->getRequest()->headers->get("Authorization");
        if($token) {
            $blackList = $this->em->getRepository("TaskTrackBundle:Blacklist");
            $blackListed = $blackList->isBlackListed($token);
            if($blackListed) {
                throw new AccessDeniedException;
            }
        }
    }
    
    public static function getSubscribedEvents(): array {
        return [
            "kernel.request" => 'onKernelRequest'
        ];
    }

}
