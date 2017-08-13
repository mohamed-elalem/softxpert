<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Service;

namespace TaskTrackBundle\Service;

use Doctrine\ORM\EntityManager;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Constants\Status;
use Symfony\Component\Config\Definition\Exception\Exception;
use JMS\Serializer\Serializer;
use TaskTrackBundle\Entity\Challenge;
use TaskTrackBundle\Graphs\Graph;

class ChallengeService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
    }
    
    public function getMyChallenges($user_id, $paginator, $page, $itemsPerPage) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenges = $challengeRepository->getUserChallenges($user_id, $paginator, $page, $itemsPerPage);
        $total = $challengeRepository->getUserChallenges($user_id, $paginator, $page, $itemsPerPage, true);
        
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "challenges" => $challenges,
                "total" => $total,
                "itemsPerPage" => $itemsPerPage
                ]
        ];
    }
    
    public function createNewChallenge($user_id, $title, $duration, $description) {
        
        if($duration <= 0) {
            throw new Exception("Duration cannot be less than 1");
        }
        
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $user = $userRepository->find($user_id);
        
        $challengeRepository->addNewChallenge($user, $title, $duration, $description);
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function updateChallenge($user_id, $challenge_id, $duration, $description) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $userRepsitory = $this->em->getRepository("TaskTrackBundle:User");
        
        $challenge = $challengeRepository->getChallenge($challenge_id);
        $user = $userRepsitory->getUser($user_id);
        
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
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
    
    public function addChallengeChild($parent_id, $child_id, Graph $graph) {
        
        if($parent_id == $child_id) {
            throw new Exception("Error self referencing is forbidden");
        }
        
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        
        $parent = $challengeRepository->find($parent_id);
        $child = $challengeRepository->find($child_id);

        if($parent->getChildren()->contains($child)) {
            throw new Exception("Error Connection already exists");
        }
        
        $edgeList = $this->getEdgeList([$challengeRepository->find($parent_id), $challengeRepository->find($child_id)], true);
        $edgeList[] = [(int)$parent_id, (int)$child_id];
        
        $graph->setup($edgeList);
        
        $data = [];
        
        $valid = $graph->checkForCycles();
        
        if($valid) {
            $challengeRepository->makeConnection($parent_id, $child_id);
            $data["code"] = Status::STATUS_SUCCESS;
            $data["extra"] = ["valid" => true];
        }
        else {
            $cycles = $graph->getCycles();
            $challengeTitles = $this->getCorrospondingTitles($cycles);
            $data["code"] = Status::STATUS_FAILURE;
            $data["err_code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
            $data["extra"] = [
                "valid" => false,
                "cycles" => $challengeTitles
            ];
        }
        return $data;
    }
    
    public function getUnassignedChallenges($supervisor_id, $user_id, $paginator, $page, $itemsPerPage) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenges = $taskRepository->getUnassignedChallenges($supervisor_id, $user_id, $paginator, $page, $itemsPerPage);
        $total = $taskRepository->getUnassignedChallenges($supervisor_id, $user_id, $paginator, $page, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "challenges" => $challenges,
                "total" => $total,
                "itemsPerPage" => $itemsPerPage
            ]
        ];
    }
    
    public function getChallengeChildren($supervisor_id, $challenge_id, $paginator, $page, $itemsPerPage) {
        $taskRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenges = $taskRepository->getChallengeChildren($supervisor_id, $challenge_id, $paginator, $page, $itemsPerPage);
        $total = $taskRepository->getChallengeChildren($supervisor_id, $challenge_id, $paginator, $page, $itemsPerPage, true);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => [
                "challenges" => $challenges,
                "total" => $total,
                "itemsPerPage" => $itemsPerPage
            ]
        ];
    }
    
    public function deleteChallenge($supervisor_id, $challenge_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenge = $challengeRepository->getChallenge($challenge_id);
        if($challenge->getSupervisor()->getId() != $supervisor_id) {
            throw new Exception("Trying to delete non owned challenge");
        }
        $data = $challengeRepository->deleteChallenge($challenge_id);
        return [
            "code" => Status::STATUS_SUCCESS,
        ];
    }
    
    public function getSingleChallenge($challenge_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenge = $challengeRepository->getChallenge($challenge_id);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => ["challenge" => $challenge]
        ];
    }
    
    private function getChallengeIdx($stack) {
        $challengeIdx = [];
        foreach ($stack as $challenge) {
            $challengeIdx[$challenge->getId()] = true;
        }
        return $challengeIdx;
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
    
    private function getCorrospondingTitles($cycles) {
        $ids = $this->getMergedIdArray($cycles);
        $titles = $this->getTitles($ids);
        $cyclesWithTitles = $this->getCyclesWithTitles($cycles, $titles);
        return $cyclesWithTitles;
    }
    
    private function getMergedIdArray($cycles) {
        $ids = [];
        foreach($cycles as $cycle) {
            if(count($cycle) > 1) {
                $ids = array_merge($ids, $cycle);
            }
        }
        return $ids;
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
    
    private function getCyclesWithTitles($cycles, $titles) {
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
}
