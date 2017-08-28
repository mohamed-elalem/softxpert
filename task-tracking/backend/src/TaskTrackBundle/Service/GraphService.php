<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Service;
use TaskTrackBundle\Graphs\Graph;
use Doctrine\ORM\EntityManager;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;


/**
 * Description of GraphService
 *
 * @author mohamedelalem
 */
class GraphService {
    
    private $em;
    
    public function __constructor($em) {
        $this->em = $em;
    }
    
    
    protected function getTitles($ids) {
        
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $idAndTitles = $challengeRepository->getIDAndTitles($ids);
        $titles = [];
        foreach($idAndTitles as $row) {
            $titles[$row["id"]] = $row["title"];
        }
        return $titles;
    }
    
    protected function getChallengeToTask($tasks) {
        $challengeToTask = [];
        foreach ($tasks as $task) {
            $challenge = $task->getChallenge();
            $challengeToTask[$challenge->getId()] = $task;
        }
        return $challengeToTask;
    }
    
    protected function getTaskToChallenge($tasks) {
        $taskToChallenge = [];
        foreach($tasks as $task) {
            $taskToChallenge[$task->getId()] = $task->getChallenge()->getId();
        } 
        return $taskToChallenge;
    }

    protected function getChallengeIdx($stack) {
        $challengeIdx = [];
        foreach ($stack as $challenge) {
            $challengeIdx[$challenge->getId()] = true;
        }
        return $challengeIdx;
    }

    protected function getInitialStack($tasks) {
        $stack = [];
        foreach ($tasks as $task) {
            $challenge = $task->getChallenge();
            $stack[] = $challenge;
        }
        return $stack;
    }

    protected function getEdgeListByTasks($tasks) {
        
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

    protected function getEdgeListByChallenge($challenge_id) {
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

    protected function getEdgeList($stack, $deep = false) {
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
    
    
    protected function getTitlesOfPriority($priorities, $titles) {
        
        foreach($priorities as $idx => $priority) {
            $priorities[$idx][0] = $titles[$priorities[$idx][0]];
        }
        return $priorities;
    }
    
    protected function initializeGraph($tasks, Graph $graph) {
        $graph->setup($this->getEdgeListByTasks($tasks));
        return $graph;
    }
    
    protected function handleValidTaskGraph(Graph $graph, $adjList, $taskToChallenge, $recommended = false) {
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
    
    protected function handleInvalidTaskGraph(Graph $graph) {
        
        $extra = [];
        
        $extra["cycles"] = $graph->getCycles();
        $extra["Acyclic"] = false;
        
        $data = [];
        $data["code"] = Status::STATUS_FAILURE;
        $data["err_code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
        $data["extra"] = $extra;
        return $data;
    }
    
        protected function getCyclesWithTitles($cycles, $titles) {
        $cyclesWithTitles = [];
        foreach($cycles as $cycle) {
            $curIndex = count($cyclesWithTitles);
            if(count($cycle) > 1) {
                $cyclesWithTitles[] = [];
                foreach($cycle as $id) {
                    $cyclesWithTitles[$curIndex][] = $titles[$id];
                }
            }
        }
        return $cyclesWithTitles;
    }
    
    protected function getCorrospondingTitles($cycles) {
        $ids = $this->getMergedIdArray($cycles);
        $titles = $this->getTitles($ids);
        $cyclesWithTitles = $this->getCyclesWithTitles($cycles, $titles);
        return $cyclesWithTitles;
    }
    
    protected function getMergedIdArray($cycles) {
        $ids = [];
        foreach($cycles as $cycle) {
            if(count($cycle) > 1) {
                $ids = array_merge($ids, $cycle);
            }
        }
        return $ids;
    }
}
