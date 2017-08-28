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
//use Symfony\Component\Config\Definition\Exception\Exception;
use JMS\Serializer\Serializer;
use TaskTrackBundle\Entity\Challenge;
use TaskTrackBundle\Graphs\Graph;
//use TaskTrackBundle\Exceptions\GenericException;
//use \TaskTrackBundle\Exceptions\ChallengeDurationException;
//use TaskTrackBundle\Exceptions\SelfReferenceException;
use TaskTrackBundle\Exceptions;

class ChallengeService extends GraphService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        parent::__constructor($em);
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
        
        $data = [];
        
        if($duration <= 0) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_FORM_VALIDATION_ERROR,
//                "err_message" => "Challenge duration cannot be less than 1"
//            ];
            throw new \TaskTrackBundle\Exceptions\ChallengeDurationException();
        }
        else {
            $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
            $userRepository = $this->em->getRepository("TaskTrackBundle:User");
            $user = $userRepository->find($user_id);

            $challengeRepository->addNewChallenge($user, $title, $duration, $description);
        
            $data = ["code" => Status::STATUS_SUCCESS];
        }
        return $data;
    }
    
    public function updateChallenge($user_id, $challenge_id, $duration, $description) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $userRepsitory = $this->em->getRepository("TaskTrackBundle:User");
        
        $challenge = $challengeRepository->getChallengeAsEntity($challenge_id);
        $user = $userRepsitory->getUser($user_id);
        $data = [];
        
        if(! $challenge) {
            $data = [
                "code" => Status::STATUS_FAILURE,
                "err_code" => Status::ERR_CHALLENGE_NOT_EXIST,
            ];
//            throw new Resour
        }
        else if(! $user->getChallenges()->contains($challenge)) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_CHALLENGE_OWNER,
//            ];
            throw new \TaskTrackBundle\Exceptions\ChallengeOwnerException;
        }
        else {
            if($description) {
                $data["description"] = $description;
            }
            if($duration) {
                $data["duration"] = $duration;
            }

            $challengeRepository->updateChallenge($challenge_id, $data);
        
            $data = [
                "code" => Status::STATUS_SUCCESS
            ];
        }
        return $data;
    }
    
    public function addChallengeChild($parent_id, $child_id, Graph $graph) {
        
        $data = [];
        
        if($parent_id == $child_id) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_FORM_VALIDATION_ERROR,
//                "err_message" => "Self reference is prohibited",
//            ];
            throw new Exceptions\SelfReferenceException();
        }
        else {
            $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");

            $parent = $challengeRepository->find($parent_id);
            $child = $challengeRepository->find($child_id);

            if($parent->getChildren()->contains($child)) {
//                $data = [
//                    "code" => Status::STATUS_FAILURE,
//                    "err_code" => Status::ERR_FORM_VALIDATION_ERROR,
//                    "err_message" => "Challenges are already connected",
//                ];
                throw new \TaskTrackBundle\Exceptions\ConnectionExistException();
            }
            else {
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
//                    $data["code"] = Status::STATUS_FAILURE;
//                    $data["err_code"] = Status::ERR_INVALID_CHALLENGES_STRUCTURE;
//                    $data["extra"] = [
//                        "valid" => false,
//                        "cycles" => $challengeTitles
//                    ];
                    
                    throw new \TaskTrackBundle\Exceptions\CircularDependencyException(null, ["valid" => false, "cycles" => $challengeTitles]);
                }
            }
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
        $challenge = $challengeRepository->getChallengeAsEntity($challenge_id);
        $data = [];
        if(! $challenge) {
            throw new Exceptions\ResourceNotFoundException;
        }
        else if($challenge->getSupervisor()->getId() != $supervisor_id) {
//            $data = [
//                "code" => Status::STATUS_FAILURE,
//                "err_code" => Status::ERR_CHALLENGE_OWNER
//            ];
            throw new Exceptions\ChallengeOwnerException;
        }
        else {
            $challengeRepository->deleteChallenge($challenge_id);
        }
        return $data;
    }
    
    public function getSingleChallenge($challenge_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challenge = $challengeRepository->getChallengeAsArray($challenge_id);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => ["challenge" => $challenge]
        ];
    }
    
//    private function getChallengeIdx($stack) {
//        $challengeIdx = [];
//        foreach ($stack as $challenge) {
//            $challengeIdx[$challenge->getId()] = true;
//        }
//        return $challengeIdx;
//    }
//    
//    private function getEdgeList($stack, $deep = false) {
//        $challengeIdx = $this->getChallengeIdx($stack);
//        $childUsed = [];
//        $edgeList = [];
//        while (count($stack)) {
//            $child = array_pop($stack);
//            if (! isset($childUsed[$child->getId()])) {
//                $childUsed[$child->getId()] = true;
//                $parents = $child->getParents();
//                foreach ($parents as $parent) {
//                    if ($deep || isset($challengeIdx[$parent->getId()])) {
//                        $edgeList[] = [$parent->getId(), $child->getId()];
//                        $stack[] = $parent;
//                    }
//                }
//            }
//        }
//        return $edgeList;
//    }
//    
//    private function getCorrospondingTitles($cycles) {
//        $ids = $this->getMergedIdArray($cycles);
//        $titles = $this->getTitles($ids);
//        $cyclesWithTitles = $this->getCyclesWithTitles($cycles, $titles);
//        return $cyclesWithTitles;
//    }
//    
//    private function getMergedIdArray($cycles) {
//        $ids = [];
//        foreach($cycles as $cycle) {
//            if(count($cycle) > 1) {
//                $ids = array_merge($ids, $cycle);
//            }
//        }
//        return $ids;
//    }
//    
//    private function getTitles($ids) {
//        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
//        $idAndTitles = $challengeRepository->getIDAndTitles($ids);
//        $titles = [];
//        foreach($idAndTitles as $row) {
//            $titles[$row["id"]] = $row["title"];
//        }
//        return $titles;
//    }
//    
//    private function getCyclesWithTitles($cycles, $titles) {
//        $cyclesWithTitles = [];
//        foreach($cycles as $cycle) {
//            $curIndex = count($cyclesWithTitles);
//            if(count($cycle) > 1) {
//                $cyclesWithTitles[] = [];
//                foreach($cycle as $id) {
//                    $cyclesWithTitles[$curIndex][] = $titles[$id];
//                }
//            }
//        }
//        return $cyclesWithTitles;
//    }
}
