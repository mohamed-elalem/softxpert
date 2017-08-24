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
        
        if ($registered) {
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_USER_EXIST;
        }
        else {
            $userRepository->addNewUser($name, $username, $email, $password, $role);
            $data["code"] = Status::STATUS_SUCCESS;
        }
        return $data;
    }
    
    public function getAllUsers($paginator, $page, $itemsPerPage) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        dump($userRepository->getUsersExceptRole(Role::ADMIN, $paginator, $page, $itemsPerPage));
        die();
        $users = $this->unsetPasswords($userRepository->getUsersExceptRole(Role::ADMIN, $paginator, $page, $itemsPerPage));
        $pageCount = $userRepository->getUsersExceptRole(Role::ADMIN, $paginator, $page, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "users" => $users,
                "pagesCount" => $pageCount,
                "itemsPerPage" => $itemsPerPage
                ]
        ];
    }
    
    public function getAllUsersByRole($role, $paginator, $page, $itemsPerPage) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
             
        $users = $this->unsetPasswords($userRepository->getUsersByRole($role, $paginator, $page, $itemsPerPage));
        $total = $userRepository->getUsersByRole($role, $paginator, $page, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "users" => $users,
                "itemsPerPage" => $itemsPerPage,
                "total" => $total
            ]
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
        $user = $userRepository->find($id);
        if($user->getRole() == Role::ADMIN) {
            throw new \TaskTrackBundle\Exceptions\ActionForbiddenException();
        }
        $userRepository->deleteUser($id);
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function updateUserInfo($user_id, $password, $password_confirmation, $email, $name) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        
        $data = [];
        
        if($password && $password != $password_confirmation) {
            $data = [
                "code" => Status::STATUS_FAILURE,
                "err_code" => Status::ERR_FORM_VALIDATION_ERROR,
                "err_message" => "Confirmation password doesn't match"
            ];
        }
        else {
            if($password) {
                $data["password"] = $password;
            }
            if($email) {
                $data["email"] = $email;
            }
            if($name) {
                $data["name"] = $name;
            }
            $userRepository->updateUser($user_id, $data);
            $data = ["code" => Status::STATUS_SUCCESS];
        }
        return $data;
    }
    
    public function getFilteredUsers($filter, $paginator, $pages, $itemsPerPage) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $users = $this->unsetPasswords($userRepository->getFilteredUsers($filter, $paginator, $pages, $itemsPerPage));
        $total = $userRepository->getFilteredUsers($filter, $paginator, $pages, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => ["users" => $users, "total" => $total, "itemsPerPage" => $itemsPerPage]
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
