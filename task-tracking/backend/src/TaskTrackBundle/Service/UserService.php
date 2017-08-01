<?php

namespace TaskTrackBundle\Service;

use Doctrine\ORM\EntityManager;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;
use Symfony\Component\Config\Definition\Exception\Exception;
use JMS\Serializer\Serializer;
use TaskTrackBundle\Constants\Role; 

class UserService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
    }
    
    public function logout($token) {
        $blackList = $this->em->getRepository("TaskTrackBundle:Blacklist");
        $blackList->blackListToken($token);
        return [
            "code" => Status::STATUS_SUCCESS,
        ];
    }
    
    public function getAuthenticatedUser($user_id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $users = $this->unsetPasswords($userRepository->getUser($user_id));
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
    
    public function getAllUsers($paginator, $page, $itemsPerPage) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        
        $users = $this->unsetPasswords($userRepository->getUsersExceptRole(Role::ADMIN, $paginator, $page, $itemsPerPage));
        $pageCount = $userRepository->getUsersExceptRole(Role::ADMIN, $paginator, $page, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "users" => $users,
                "pageCount" => $pageCount,
                ]
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
        $user = $this->unsetPasswords($userRepository->getUser($id));
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
    
    public function getFilteredUsers($filter, $paginator, $pages, $itemsPerPage) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $users = $userRepository->getFilteredUsers($filter, $paginator, $pages, $itemsPerPage);
        $pagesCount = $userRepository->getFilteredUsers($filter, $paginator, $pages, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => ["users" => $users, "pagesCount" => $pagesCount]
        ];
    }
   
        
    private function unsetPasswords($users) {
        foreach($users as $idx => $user) {
            unset($user["password"]);
            
            if($user["role"] == Role::ADMIN) {
                $user["role"] = "Administrator";
            }
            else if($user["role"] == Role::SUPERVISOR) {
                $user["role"] = "Supervisor";
            }
            else {
                $user["role"] = "Trainee";
            }
            
            $users[$idx] = $user;
        }
        return $users;
    }
}
