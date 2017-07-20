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

class ChallengeService {

    private $em;

    public function __construct(EntityManager $em, Serializer $serializer) {
        $this->em = $em;
        ResponseHandler::setSerializer($serializer);
    }
    
    public function getMyChallenges($user_id) {
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $user = $userRepository->getUser($user_id);
        return [
            "code" => Status::STATUS_SUCCESS,
            "extra" => $user->getChallenges()
        ];
    }
    
    public function createNewChallenge($user_id, $title, $duration, $description) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $userRepository = $this->em->getRepository("TaskTrackBundle:User");
        $user = $userRepository->getUser($user_id);
        
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
    
    public function addChallengeChild($parent_id, $child_id) {
        $challengeRepository = $this->em->getRepository("TaskTrackBundle:Challenge");
        $challengeRepository->makeConnection($parent_id, $child_id);
        
        return [
            "code" => Status::STATUS_SUCCESS
        ];
    }
}
