<?php

namespace TaskTrackBundle\Service;

use Doctrine\ORM\EntityManager;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Constants\Role;
use Symfony\Component\Config\Definition\Exception\Exception;
use \TaskTrackBundle\Graphs\Graph;
use JMS\Serializer\Serializer;

class UserService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
        ResponseHandler::setSerializer($serializer);
    }

    public function getAuthenticatedUser($user) {
        return $user;
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
    
    public function getUserTasks($user_id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $tasks = $userRepository->getUser($user_id)->getTasks();
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $tasks
        ];
    }
    
    public function getUserTask($user_id, $challenge_id) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $tasks = $taskRepository->getUserTask($user_id, $challenge_id);
        
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $tasks
        ];
    }
    
    public function getMyTasks($user, Graph $graph) {
        $graph->setup($this->getEdgeList($user->getTasks()));
        $data = ["tasks" => $user->getTasks(), "adjList" => $graph->getAdjList()];
        $valid = $graph->run();
        $data["Acyclic"] = $valid;
        $data["topoSort"] = $graph->getTopoSort();
        $data["cycles"] = $graph->getStronglyConnectedComponents();
//        dump($data);
//        die();
        return ResponseHandler::handle($response, Status::STATUS_SUCCESS, $data);
    }
    
    public function getMyChallenges($user) {
        return ResponseHandler::handle($response, Status::STATUS_SUCCESS, $user->getChallenges());
    }
    
    public function updateUserTaskScore($user_id, $challenge_id, $score) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $taskRepository->updateScore($user_id, $challenge_id, $score);
        
        return ResponseHandler($response, Status::STATUS_SUCCESS);
    }
    
    public function updateUserTaskDuration($user_id, $challenge_id, $duartion) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $taskRepository->updateDuration($user_id, $challenge_id, $duration);
        
        return ResponseHandler::handle(new Response, Status::STATUS_SUCCESS);
    }
    
    public function updateTaskDone($user_id, $challenge_id, $done) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $taskRepository->updateDone($user_id, $challenge_id, $done);
        
        return ResponseHandler::handle(Status::STATUS_SUCCESS);
    }
    
    public function createNewChallenge($user, $title, $duration, $description) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
    
        $challengeRepository->addNewChallenge($user, $title, $duration, $description);
        
        return ResponseHandler::handle($response, Status::STATUS_SUCCESS);
    }
    
    public function createNewTask($supervisor, $user_id, $challenge_id) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        
        $task = $taskRepository->checkIfTaskExists($user_id, $challenge_id);
        if(! $task) {
            $user = $userRepository->getUserByRole($user_id, Role::TRAINEE);
            $challenge = $challengeRepository->getChallenge($challenge_id);
            
            if($supervisor->getChallenges()->contains($challenge)) {
            
                $taskRepository->addNewTask($user, $challenge);
            
                $response = ResponseHandler::handle($response, Status::STATUS_SUCCESS);
            }
            else {
                $response = ResponseHandler::handle($response, Status::STATUS_FAILURE, [], Status::ERR_CHALLENGE_OWNER);
            }
        }
        else {
            
            $response = ResponseHandler::handle($response, Status::STATUS_FAILURE, [], Status::ERR_TASK_EXIST);
        }
        return $response;
    }
    
    public function updateUserInfo($user, $password, $password_confirmation, $email, $name) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        
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
        
        return ResponseHandler::handle($response, Status::STATUS_SUCCESS);
    }
   
    public function updateChallenge($user, $challenge_id, $duration, $description) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenge = $challengeRepository->getChallenge($challenge_id);
        
        if(! $challenge) {
            throw new Exception("Challenge " . $challenge_id . " wasn't found");
        }
        
        if(! $user->getChallenges()->contains($challenge)) {
            throw new Exception("You cannot update a challenge that's not owned by you");
        }
        
        $data = [];
        
        if($description) {
            $data["description"] = $description;
        }
        if($duration) {
            $data["duration"] = $duration;
        }
        
        $challengeRepository->updateChallenge($challenge_id, $data);
        
        return ResponseHandler::handle($response, Status::STATUS_SUCCESS);
    }
    
    public function addChallengeChild($parent_id, $child_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challengeRepository->makeConnection($parent_id, $child_id);
        return ResponseHandler::handle($response, Status::STATUS_SUCCESS);
    }
        
    private function unsetPasswords($users) {
        foreach($users as $user) {
            $user->setPassword(null);
        }
        return $users;
    }
    
    private function getEdgeList($tasks) {
        $edgeList = [];
        $childUsed = [];
        $stack = [];
        foreach($tasks as $task) {
            $stack[] = $task->getChallenge();
        }
        while(count($stack)) {
            $child = array_pop($stack);
            if(! isset($childUsed[$child->getId()])) {
                $childUsed[$child->getId()] = true;
                $parents = $child->getParents();
                foreach($parents as $parent) {
                    $edgeList[] = [$parent->getId(), $child->getId()];
                    $stack[] = $parent;
                }
            }
        }
        return $edgeList;
    }
    
    private function getReturnSample() {
        return [
            "status" => -1,
            "data" => [],
            "err_code", -1,
            "err_message", ''
        ];
    }

}
