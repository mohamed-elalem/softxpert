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

class TaskService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
    }

    public function getUserTasks($user_id, $paginator, $page, $itemsPerPage) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getTraineeUnfinishedTasks($user_id, $paginator, $page, $itemsPerPage);
        $total = $taskRepository->getTraineeUnfinishedTasks($user_id, $paginator, $page, $itemsPerPage, true);
        
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
        $tasks = $taskRepository->getTraineeUnfinishedTasks($user_id);
        $graph->setup($this->getEdgeListByTasks($tasks));
        $adjList = $graph->getAdjList();
        $valid = $graph->checkForCycles();
        if ($valid) {
            $graph->topologicalSort($adjList);
            $extra["tasks"] = $graph->getTopoSort();
            $extra["priority"] = $graph->getTaskPriority();
        } else {
            $extra["cycles"] = $graph->getCycles();
        }
        $extra["Acyclic"] = $valid;

        $data = [];
        $data["code"] = Status::STATUS_SUCCESS;
        $data["extra"] = $extra;
        if (!$valid) {
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
        }

        return $data;
    }
    
    public function deleteTask($supervisor_id, $task_id) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $task = $taskRepository->find($task_id);
        if(! $task) {
            throw new Exception("Task with id $task_id is not found");
        }
        else if($task->getSupervisor()->getId() != $supervisor_id) {
            throw new Exception("You're not the supervisor of this task");
        }
        $data = $taskRepository->deleteTask($task_id);
        return [
            "code" => Status::STATUS_SUCCESS,
        ];
    }

    public function updateUserTaskScore($task_id, $score) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");

        $taskRepository->updateScore($task_id, $score);

        return [
            "code" => Status::STATUS_SUCCESS
        ];
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
            
            for ($i = $n - 1; $i >= 0; $i--) {
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
            $data["code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
            $data["extra"] = $graph->getCycles();
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
        } else if ($task) {
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_TASK_EXIST;
        } else if (!$allOk) {
            
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_MISSING_TASKS;
            $data["extra"] = [
                "challengesToAdd" => $challengesToAdd,
                "order" => "asc",
                "taskPriorities" => $priority,
            ];
            
            /**
             * Garbage code will be removed after testing
             */
            /*$challengeNames = [];
            $challenges = $supervisor->getChallenges();
            $challengeAssociativeArray = [];
            foreach($challenges as $challenge) {
                $challengeAssociativeArray[$challenge->getId()] = $challenge;
            }
            foreach($challengesToAdd as $id) {
                $challengeNames[] = $challengeAssociativeArray[$id]->getTitle();
            }
            
            $data["extra"]["names"] = $challengeNames;
            */
            /**
             * End of garbage
             */
            
            
            
        }
        return $data;
    }
    
    public function getFilteredTasks($filter, $paginator, $pages, $itemsPerPage) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Task");
        $tasks = $taskRepository->getFilteredTasks($filter, $paginator, $pages, $itemsPerPage);
        $pagesCount = $taskRepository->getFilteredTasks($filter, $paginator, $pages, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => ["tasks" => $tasks, "pagesCount" => $pagesCount]
        ];
    }
    

    private function getChallengeToTask($tasks) {
        $challengeToTask = [];
        foreach ($tasks as $task) {
            $challenge = $task->getChallenge();
            $challengeToTask[$challenge->getId()] = $task;
        }
        return $challengeToTask;
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
            $edgeList[$idx] = [$challengeToTask[$u]->getId(), $challengeToTask[$v]->getId()];
        }
        return $edgeList;
    }

    private function getEdgeListByChallenge($challenge_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenge = $challengeRepository->getChallenge($challenge_id);
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
                foreach ($parents as $parent) {
                    if ($deep || isset($challengeIdx[$parent->getId()])) {
                        $edgeList[] = [$parent->getId(), $child->getId()];
                        $stack[] = $parent;
                    }
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
}
