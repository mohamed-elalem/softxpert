<?php

namespace TaskTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TaskTrackBundle\Entity\User;
use TaskTrackBundle\Constants\Role;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\ResponseHandler;

class UserController extends Controller
{
    
    public function registerAction(Request $request)
    {
        return $this->register($request, Role::TRAINEE);
    }
    
    public function register(Request $request, $role) {
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        $encoder = $this->container->get('security.password_encoder');

        $username = $request->request->get("username");
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $name = $request->request->get("name");
        
        $registered = count($userRepository->checkIfRegistered($username, $email)) == 1;
        
        $response = new Response();
        
        if(! $registered) {
            $userRepository->addNewUser($name, $username, $email, $encoder->encodePassword(new User(), $password), $role);
            $response->setContent(ResponseHandler::getInstance()->handle(Status::SUCCESS, "json"));
            return $response;    
        }
        else {
            $response->setContent(ResponseHandler::getInstance()->handle(Status::EXIST, "json"));
            $response->setStatusCode(Status::RESPONSE_CODES[Status::EXIST]);
        }
        return $response;
    }
    
    public function registerSupervisorAction(Request $request) {
        return $this->register($request, Role::SUPERVISOR);
    }
    
    public function getUserInfoAction() {
        
        $role = "";
        $roleNumber = $this->getUser()->getRole();
        
        if($roleNumber == Role::ADMIN) {
            $role = "admin";
        }
        else if($roleNumber == Role::SUPERVISOR) {
            $role = "supervisor";
        }
        
        return new Response(ResponseHandler::getInstance()->handle(Status::SUCCESS, "json", ["name" => $this->getUser()->getName(), "role" => $role]));
    }
    
    public function apiAction(Request $request)
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }
}
