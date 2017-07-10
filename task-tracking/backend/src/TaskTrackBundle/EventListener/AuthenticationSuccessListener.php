<?php

namespace TaskTrackBundle\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use TaskTrackBundle\Constants\Role;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;


class AuthenticationSuccessListener {

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event) {
        $data = $event->getData();
        $user = $event->getUser();
        
        $role = '';
        
        if($user->getRole() == Role::ADMIN) {
            $role = 'admin';
        }
        else if($user->getRole() == Role::SUPERVISOR) {
            $role = 'supervisor';
        }
        
        $data['role'] = $role;
        
        $event->setData($data);
    }

}
