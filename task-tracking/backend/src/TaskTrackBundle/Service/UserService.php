<?php

namespace TaskTrackBundle\Service;

use Doctrine\ORM\EntityManager;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;
use Symfony\Component\Config\Definition\Exception\Exception;
use JMS\Serializer\Serializer;

class UserService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
        ResponseHandler::setSerializer($serializer);
    }
    
    public function getAuthenticatedUser($user_id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $users = $this->unsetPasswords([ $userRepository->getUser($user_id) ]);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $users
        ];
    }
    
    public function register($username, $email, $password, $name, $role) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        
        $registered = count($userRepository->checkIfRegistered($username, $email)) == 1;
        
        if (!$registered) {
            $userRepository->addNewUser($name, $username, $email, $password, $role);
            $data["code"] = Status::STATUS_SUCCESS;
        } else {
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_USER_EXIST;
        }
        return $data;
    }
    
    public function getAllUsers() {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $users = $this->unsetPasswords($userRepository->getAllUsers());
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $users
        ];
    }
    
    public function getAllUsersByRole($role) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $users = $this->unsetPasswords($userRepository->getUsersByRole($role));
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $users
        ];
    }
    
    public function getUser($id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $user = $userRepository->getUser($id);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $user
        ];
    }
    
    public function deleteUser($id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $userRepository->deleteUser($id);
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function updateUserInfo($user_id, $password, $password_confirmation, $email, $name) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        
        $user = $userRepository->getUser($user_id);
        $data = [];
        
        if($password && $password != $password_confirmation) {
            throw new Exception("Password confirmation not equal password");
        }
        
        if($password) {
            $data["password"] = $password;
        }
        if($email) {
            $data["email"] = $email;
        }
        if($name) {
            $data["name"] = $name;
        }
        
        $userRepository->updateUser($user, $data);
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
   
        
    private function unsetPasswords($users) {
        foreach($users as $user) {
            $user->setPassword(null);
        }
        return $users;
    }
}
