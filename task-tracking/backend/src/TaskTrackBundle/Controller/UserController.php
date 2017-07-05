<?php

namespace TaskTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Constants\Role;
use Symfony\Component\HttpFoundation\Response;
use TaskTrackBundle\Entity\User;
use TaskTrackBundle\Encoder\NixillaJWTEncoder;
use TaskTrackBundle\Form\UserType;
use TaskTrackBundle\Helpers;

class UserController extends Controller
{
    
//    public function index() {
//        return $this->render("index.html.twig");
//    }
//    
    public function loginAction(Request $request) {
        $email = $request->get("email");
        $password = $request->get("password");
        
        $response = new Response();
        $responseHandler = ResponseHandler::getInstance();
        
        $user = $this->getDoctrine()->getRepository("TaskTrackBundle:User")->findOneBy(["email" => $email]);
        
        if($user) {
            $isValidPassword = $this->get("security.password_encoder")->isPasswordValid($user, $password);
  
            if(! $isValidPassword) {
                $response->setContent($responseHandler->handle(Status::FAILURE, "json"));
            }
            else {
                $token = $this->get('lexik_jwt_authentication.encoder')
                    ->encode([
                        'email' => $user->getEmail(),
                        'exp' => time() + 3600 // 1 hour expiration
                    ]);
                $response->setContent($responseHandler->handle(Status::SUCCESS, "json", ["token" => $token]));
            }
        }
        else {
            $response->setContent($responseHandler->handle(Status::FAILURE, "json"));
        }
        
        return $response;
    }
//    
//    public function showRegistrationFormAction() {
////        return $this->render("");
//    }
    
    public function registerAction(Request $request) {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user, array('csrf_protection' => false));
        
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        $encoder = $this->get("security.password_encoder");
        $responseHandler = ResponseHandler::getInstance();
        
        
        $name = $request->get("name");
        $email = $request->get("email");
        $password = $request->get("password");
        
        $user->setName($name)
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($user, $password))
                ->setRole(Role::TRAINEE);
        
        $response = new Response();
        $valid = true; // $form->isValid();
        
        if($userRepository->findOneBy(["email" => $email])) {
            $response->setContent($responseHandler->handle(Status::EXIST, "json"));
        }
        else if($valid) {
            $em->persist($user);
            $em->flush();
            $response->setContent($responseHandler->handle(Status::SUCCESS, "json"));
        }
        else {
            dump(Helpers\Helper::getFormErrors($form));
            die();
            $response->setContent($responseHandler->handle(Status::FAILURE, "json"));
        }
        $response->headers->set("Content-Type", "application/json");        
        return $response;
    }
}
