<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Service;

use Doctrine\ORM\EntityManager;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Constants\Role;
use \TaskTrackBundle\Graphs\Graph;
use JMS\Serializer\Serializer;

class TaskService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
        ResponseHandler::setSerializer($serializer);
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
    
    public function getMyTasks($user_id, Graph $graph) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $user = $userRepository->getUser($user_id);
        $graph->setup($this->getEdgeList($user));
        $adjList = $graph->getAdjList();
        $valid = $graph->checkForCycles();
        if($valid) {
            $graph->topologicalSort($adjList);
            $extra["tasks"] = $graph->getTopoSort();
            $extra["priority"] = $graph->getTaskPriority();
        }
        else {
            $extra["cycles"] = $graph->getCycles();
        }
        $extra["Acyclic"] = $valid;
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $extra
        ];
    }
    
    public function updateUserTaskScore($user_id, $challenge_id, $score) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $taskRepository->updateScore($user_id, $challenge_id, $score);
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function updateUserTaskDuration($user_id, $challenge_id, $duartion) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $taskRepository->updateDuration($user_id, $challenge_id, $duration);
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function updateTaskDone($user_id, $challenge_id, $done) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $taskRepository->updateDone($user_id, $challenge_id, $done);
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function createNewTask($supervisor_id, $user_id, $challenge_id) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        
        $supervisor = $userRepository->getUser($supervisor_id);
        $task = $taskRepository->checkIfTaskExists($user_id, $challenge_id);
        $data = [];
        if(! $task) {
            $user = $userRepository->getUserByRole($user_id, Role::TRAINEE);
            $challenge = $challengeRepository->getChallenge($challenge_id);
            
            if($supervisor->getChallenges()->contains($challenge)) {
            
                $taskRepository->addNewTask($user, $challenge);
            
                $data["code"] = Status::STATUS_SUCCESS;
            }
            else {
                $data["code"] = Status::STATUS_FAILURE;
                $data["err_code"] = Status::ERR_CHALLENGE_OWNER;
            }
        }
        else {
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_TASK_EXIST;
        }
        return $data;
    }
    
    private function getEdgeList($user) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getTraineeUnfinishedTasks($user->getId());
        $edgeList = [];
        $stack = [];
        $challengeToTask = [];
        $challengeIdx = [];
        foreach($tasks as $task) {
            $challenge = $task->getChallenge();
            $challengeToTask[$challenge->getId()] = $task;
            $stack[] = $challenge;
            $challengeIdx[$challenge->getId()] = true;
        }
        
        while(count($stack)) {
            $child = array_pop($stack);
            if(! isset($childUsed[$child->getId()])) {
                $childUsed[$child->getId()] = true;
                $parents = $child->getParents();
                foreach($parents as $parent) {
                    if(isset($challengeIdx[$parent->getId()])) {
                        $edgeList[] = [$parent->getId(), $child->getId()];
                        $stack[] = $parent;
                    }
                }
            }
        }
        foreach($edgeList as $idx => $pairs) {
            $u = $pairs[0];
            $v = $pairs[1];
            $edgeList[$idx] = [$challengeToTask[$u]->getId(), $challengeToTask[$v]->getId()];
        }
        return $edgeList;
    }
}