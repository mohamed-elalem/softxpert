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
use TaskTrackBundle\Filters\TaskInterface;
use JMS\Serializer\Serializer;
use TaskTrackBundle\Exceptions;

class TaskService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
    }

    public function getUserTasks($supervisor_id, $user_id, $paginator, $page, $itemsPerPage) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getTraineeUnfinishedTasksPaginated($supervisor_id, $user_id, $paginator, $page, $itemsPerPage);
        $total = $taskRepository->getTraineeUnfinishedTasksPaginated($supervisor_id, $user_id, $paginator, $page, $itemsPerPage, true);

        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "tasks" => $tasks,
                "total" => $total,
                "itemsPerPage" => $itemsPerPage
            ]
        ];
    }

    public function getUserTask($user_id, $challenge_id) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $task = $taskRepository->getUserTask($user_id, $challenge_id);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $task
        ];
    }

    public function getMyTasks($user_id, Graph $graph) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getTraineeUnfinishedTasksNonPaginated($user_id);
        
        $graph = $this->initializeGraph($tasks, $graph);
        
        $adjList = $graph->getAdjList();
        
        
        $valid = $graph->checkForCycles();
        
        $data = [];
        
        if ($valid) {
            $taskToChallenge = $this->getTaskToChallenge($tasks);
            $data = $this->handleValidTaskGraph($graph, $adjList, $taskToChallenge);
            
        } else {
            $data = $this->handleInvalidTaskGraph($graph);
        }
        return $data;
    }
    
    public function getMyRecommendedTasks($user_id, $graph) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getTraineeUnfinishedTasksNonPaginated($user_id);
         
        $graph = $this->initializeGraph($tasks, $graph);
        $adjList = $graph->getAdjList();
        $valid = $graph->checkForCycles();
        $data = [];
        if($valid) {
            $taskToChallenge = $this->getTaskToChallenge($tasks);
            $data = $this->handleValidTaskGraph($graph, $adjList, $taskToChallenge, true);
        }
        else {
            $data = $this->handleInvalidTaskGraph($graph);
        }
        return $data;
    }
    
    public function deleteTask($supervisor_id, $task_id) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $task = $taskRepository->find($task_id);
        $data = [];
        if(! $task) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_TASK_NOT_EXIST,
//            ];
            throw new Exceptions\ItemNotFoundException("Task " . $task_id . " Wasn't found");
        }
        else if($task->getSupervisor()->getId() != $supervisor_id) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_CHALLENGE_OWNER,
//            ];
            throw new Exceptions\ChallengeOwnerException;
        }
        else {
            $taskRepository->deleteTask($task_id);
            $data = ["code" => Status::STATUS_SUCCESS];
        }
        return $data;
    }

    public function updateUserTaskScore($task_id, $score) {
        
        $data = [];
        
        if($score < 0 || $score > 100) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_FORM_VALIDATION_ERROR,
//                "err_message" => "Score must be between 0 and 100",
//            ];
            throw new Exceptions\FormValidationException("Score must be between 0 and 100");
        }
        else { 
            $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");

            $taskRepository->updateScore($task_id, $score);
            $data = ["code" => Status::STATUS_SUCCESS];
        }
        return $data;
    }

    public function updateUserTaskDuration($task_id, $duartion) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        

        $taskRepository->updateDuration($task_id, $duration);

        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }

    public function updateTaskDone($task_id, $done) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        if(!is_bool($done)) {
            throw new Exceptions\FormValidationException("Done must be true or false");
        }
        
        $taskRepository->updateDone($task_id, $done);

        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }

    public function createNewTask($supervisor_id, $user_id, $challenge_id, Graph $graph) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $supervisor = $userRepository->find($supervisor_id);
        $trainee = $userRepository->find($user_id);

        /**
         * Checking whether this task finished its prerequisites successfully
         * or return the required prerequisites.
         * Idea is simple just use the same topological sort but on the graph transpose
         * (i.e. Flip edges direction) 
         */
//        $tasks = $taskRepository->getTraineeTasks($user_id);
        $tasks = $trainee->getTasks();
        
        $edgeList = $this->getEdgeListByChallenge($challenge_id);
        $graph->setup($edgeList);
        $valid = $graph->checkForCycles();
        $allOk = true;
        $challengesToAdd = [];
        if ($valid) {
            $graph->unsetAdjListExtraVertices();
            $adjList = $graph->getAdjList();
            $graph->topologicalSort($adjList);
            
            $topoSort = $graph->getTopoSort();
            $tmp = $graph->getTaskPriority();
            $priority = [];
            $challengeIdx = $this->getChallengeIdx($this->getInitialStack($tasks));
            $n = count($topoSort);
            
            
            if($n > 0) {
                $challengeTitles = $this->getTitles($topoSort);
            }
            for ($i = 0; $i < $n; $i++) {
                if (!isset($challengeIdx[$topoSort[$i]]) && $challenge_id != $topoSort[$i]) {
                    $challengesToAdd[] = $challengeTitles[$topoSort[$i]];
                    $allOk = false;
                    $tmp[$i][0] = $challengeTitles[$tmp[$i][0]];
                    $priority[] = $tmp[$i];
                }
            }
            
        }

        // End of checking

        $task = $taskRepository->checkIfTaskExists($supervisor_id, $user_id, $challenge_id);
        $data = [];
        if (!$valid) {
//            $data["code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
//            $data["extra"] = $graph->getCycles();
            throw new Exceptions\CircularDependencyException(null, $graph->getCycles());
        } else if (!$task && $allOk) {
            $user = $userRepository->getUserByRole($user_id, Role::TRAINEE);
            $challenge = $challengeRepository->find($challenge_id);

            if (! is_null($challenge) && $challenge->getSupervisor()->getId() == $supervisor_id) {

                $taskRepository->addNewTask($supervisor, $user, $challenge);

                $data["code"] = Status::STATUS_SUCCESS;
            } else {
                $data["code"] = Status::STATUS_FAILURE;
                $data["err_code"] = Status::ERR_CHALLENGE_OWNER;
            }
//        } else if ($task) {
//            $data["code"] = Status::STATUS_FAILURE;
//            $data["err_code"] = Status::ERR_TASK_EXIST;
            throw new Exceptions\DuplicateResourceException;
        } else if (!$allOk) {
            
//            $data["code"] = Status::STATUS_FAILURE;
//            $data["err_code"] = Status::ERR_MISSING_TASKS;
//            $data["extra"] = [
//                "challengesToAdd" => $challengesToAdd,
//                "order" => "asc",
//                "taskPriorities" => $priority,
//            ];
            
            throw new Exceptions\MissingDependenciesException(null, [
                "challengesToAdd" => $challengesToAdd,
                "order" => "asc",
                "taskPriorities" => $priority
            ]);
        }
        return $data;
    }
    
    public function getFilteredTasks($filter, $paginator, $page, $itemsPerPage) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getFilteredTasks($filter, $paginator, $page, $itemsPerPage);
        $total = $taskRepository->getFilteredTasks($filter, $paginator, $page, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => ["tasks" => $tasks, "total" => $total, "itemsPerPage" => $itemsPerPage]
        ];
    }
    
    public function toggleTaskInProgress($user_id, $task_id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        
        $user = $userRepository->find($user_id);
        $task = $taskRepository->find($task_id);
        $data = [];
        if(! $user->getTasks()->contains($task)) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_CHALLENGE_OWNER
//            ];
            throw new Exceptions\ChallengeOwnerException;
        }
        else if($task->getDone()) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_ACTION_FORBIDDEN,
//                "err_message" => "Task you requested is already done"
//            ];
            throw new Exceptions\ActionForbiddenException("Task " . $task_id . " is already done");
        }
        else {
            $task = $taskRepository->toggleTaskInProgress($task_id);
            $data = [
                "code" => Status::STATUS_SUCCESS,
            ];
        }
        return $data;
    }
    

    private function getChallengeToTask($tasks) {
        $challengeToTask = [];
        foreach ($tasks as $task) {
            $challenge = $task->getChallenge();
            $challengeToTask[$challenge->getId()] = $task;
        }
        return $challengeToTask;
    }
    
    private function getTaskToChallenge($tasks) {
        $taskToChallenge = [];
        foreach($tasks as $task) {
            $taskToChallenge[$task->getId()] = $task->getChallenge()->getId();
        } 
        return $taskToChallenge;
    }

    private function getChallengeIdx($stack) {
        $challengeIdx = [];
        foreach ($stack as $challenge) {
            $challengeIdx[$challenge->getId()] = true;
        }
        return $challengeIdx;
    }

    private function getInitialStack($tasks) {
        $stack = [];
        foreach ($tasks as $task) {
            $challenge = $task->getChallenge();
            $stack[] = $challenge;
        }
        return $stack;
    }

    private function getEdgeListByTasks($tasks) {
        
        $stack = $this->getInitialStack($tasks);
        $challengeToTask = $this->getChallengeToTask($tasks);
        
        $edgeList = $this->getEdgeList($stack);
        
        foreach ($edgeList as $idx => $pairs) {
            $u = $pairs[0];
            $v = $pairs[1];
            $parentChallengeId = $challengeToTask[$u]->getId();
            $childChallengeId = -1;
            if($v != -1) {
                $childChallengeId = $challengeToTask[$v]->getId();
            }
            $edgeList[$idx] = [$parentChallengeId, $childChallengeId];
        }
        return $edgeList;
    }

    private function getEdgeListByChallenge($challenge_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenge = $challengeRepository->getChallengeAsEntity($challenge_id);
        $stack = [$challenge];

        $edgeList = $this->getEdgeList($stack, true);
        foreach ($edgeList as $idx => $pairs) {
            $u = $pairs[0];
            $v = $pairs[1];
            $edgeList[$idx] = [$v, $u];
        }
        return $edgeList;
    }

    private function getEdgeList($stack, $deep = false) {
        $challengeIdx = $this->getChallengeIdx($stack);
        
        $childUsed = [];
        $edgeList = [];
        while (count($stack)) {
            $child = array_pop($stack);
            if (! isset($childUsed[$child->getId()])) {
                $childUsed[$child->getId()] = true;
                $parents = $child->getParents();
                if(! $parents->isEmpty()) {
                    foreach ($parents as $parent) {
                        if ($deep || isset($challengeIdx[$parent->getId()])) {
                            $edgeList[] = [$parent->getId(), $child->getId()];
                            $stack[] = $parent;
                        }
                    }
                }
                else {
                    $edgeList[] = [$child->getId(), $child->getId()];
                }
                
            }
        }
        return $edgeList;
    }
    
    private function getTitles($ids) {
        
        
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $idAndTitles = $challengeRepository->getIDAndTitles($ids);
        $titles = [];
        foreach($idAndTitles as $row) {
            $titles[$row["id"]] = $row["title"];
        }
        return $titles;
    }
    
    private function getTitlesOfPriority($priorities, $titles) {
        
        foreach($priorities as $idx => $priority) {
            $priorities[$idx][0] = $titles[$priorities[$idx][0]];
        }
        return $priorities;
    }
    
    private function initializeGraph($tasks, Graph $graph) {
        $graph->setup($this->getEdgeListByTasks($tasks));
        return $graph;
    }
    
    private function handleValidTaskGraph(Graph $graph, $adjList, $taskToChallenge, $recommended = false) {
        $graph->topologicalSort($adjList);
        
        $extra = [];
        
        $topoSort = $graph->getTopoSort();
        
        
        $priorities = $graph->getTaskPriority();
        
        
        $n = count($topoSort);
        for($i = 0; $i < $n; $i++) {
            if($recommended && $priorities[$i][1] > 0) {
                unset($topoSort[$i]);
                unset($priorities[$i]);
            }
            else {
                $topoSort[$i] = $taskToChallenge[$topoSort[$i]];
                $priorities[$i][0] = $taskToChallenge[$priorities[$i][0]];
            }
        }
        
        $extra["tasks"] = $this->getTitles($topoSort);
        $extra["priority"] = $this->getTitlesOfPriority($priorities, $extra["tasks"]);
    
        $extra["Acyclic"] = true;

        $data = [];
        $data["code"] = Status::STATUS_SUCCESS;
        $data["extra"] = $extra;
        
        return $data;
        
    }
    
    private function handleInvalidTaskGraph(Graph $graph) {
        
        $extra = [];
        
        $extra["cycles"] = $graph->getCycles();
        $extra["Acyclic"] = false;
        
        $data = [];
        $data["code"] = Status::STATUS_FAILURE;
        $data["err_code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
        $data["extra"] = $extra;
        return $data;
    }
    
}
