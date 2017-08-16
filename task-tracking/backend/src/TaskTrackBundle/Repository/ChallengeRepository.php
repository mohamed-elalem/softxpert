<?php

namespace TaskTrackBundle\Repository;

use TaskTrackBundle\Entity\Challenge;

/**
 * ChallengeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChallengeRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function addNewChallenge($user, $title, $duration, $description) {
        $em = $this->getEntityManager();
        $challenge = new Challenge;
        $challenge->setDuration($duration);
        $challenge->setDescription($description);
        $challenge->setTitle($title);
        $challenge->setSupervisor($user);
        $em->persist($challenge);
        $em->flush();
        
    }
    
    public function getChallenge($id) {
        return $this->createQueryBuilder("c")
                ->select()
                ->where("c.id = :id")
                ->setParameter("id", $id)
                ->getQuery();
    }
    
    public function getChallengeAsEntity($id) {
        return $this->find($id);
    }
    
    public function getChallengeAsArray($id) {
        return $this->getChallenge($id)->getArrayResult();
    }
    
    public function updateChallenge($challenge_id, $data) {
        $q = $this->createQueryBuilder("c")
                ->update();
        
        foreach($data as $key => $value) {
            $q = $q->set("c." . $key, "'$value'");
        }
        
        $q->where("c.id = :challenge_id")
                ->setParameter("challenge_id", $challenge_id)
                ->getQuery()->execute();
    }
    
    public function makeConnection($parent_id, $child_id) {
        $em = $this->getEntityManager();
        $parent = $this->find($parent_id);
        $child = $this->find($child_id);
        $child->addParent($parent);
        $parent->addChild($child);
        $em->persist($child);
        $em->persist($parent);
        $em->flush();
    }
    
    public function getUserChallenges($user_id, $paginator, $page, $itemsPerPage, $count = false) {
        $qb = $this->createQueryBuilder("c");
        $challenges = $qb
                ->select()
                ->where("c.supervisor = :user_id")
                ->setParameter("user_id", $user_id);
        
        if($count) {
            return $paginator->getCount($challenges);
        }
        else {
            return $paginator->getResult($challenges, $page, $itemsPerPage);
        }
        
    }
    
    public function getChallengeBySupervisor($supervisor_id, $challenge_id) {
        $challenge = $this->createQueryBuilder("c")
                ->select()
                ->where("c.supervisor = :supervisor_id and c.id = :challenge_id") 
                ->setParameter("supervisor_id", $supervisor_id)
                ->setParameter("challenge_id", $challenge_id)
                ->getQuery()->getResult();
        return $challenge;
    }
    
    public function getUnassignedChallenges($supervisor_id, $trainee_id, $paginator, $page, $itemsPerPage, $count = false) {
        $ids = $this->createQueryBuilder("sc")
                ->select("sc.id")
                ->join("sc.tasks", "t")
                ->where("t.user = :trainee_id")
                ->setParameter("trainee_id", $trainee_id)
                ->getQuery()
                ->getResult();
        
        foreach($ids as $idx => $row) {
            $ids[$idx] = $row["id"];
        }
        
        if(empty($ids)) {
            $ids = [0];
        }
         
        $qb = $this->createQueryBuilder("c");
        $challenges = $qb->select()
                ->where($qb->expr()->notIn("c.id", $ids))
                ->andWhere("c.supervisor = :supervisor_id")
                ->setParameter("supervisor_id", $supervisor_id);


        if($count) {
            return $paginator->getCount($challenges);
        }
        return $paginator->getResult($challenges, $page, $itemsPerPage);
    }
    
    public function getChallengeChildren($supervisor_id, $challenge_id, $paginator, $page, $itemsPerPage, $count = false) {
        $ids = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("c.id")
                ->from("TaskTrackBundle:Challenge", "p")
                ->join("p.children", "c")
                ->where("p.id = :challenge_id")
                ->orWhere("c.id = :challenge_id")
                ->setParameter("challenge_id", $challenge_id)
                ->getQuery()->getResult();
        
        foreach($ids as $idx => $row) {
            $ids[$idx] = $row["id"];
        }
        
        if(empty($ids)) {
            $ids = [0];
        }
        
        $qb = $this->createQueryBuilder("pp");
        $children = $qb->select()
                ->where($qb->expr()->notIn("pp.id", $ids))
                ->andWhere("pp.supervisor = :supervisor_id")
                ->andWhere("pp.id != :challenge_id")
                ->setParameter("challenge_id", $challenge_id)
                ->setParameter("supervisor_id", $supervisor_id);
        
        if($count) {
            return $paginator->getCount($children);
        }
        return $paginator->getResult($children, $page, $itemsPerPage);     
    }
    
    public function deleteChallenge($challenge_id) {
        return $this->createQueryBuilder("c")
                ->delete()
                ->where("c.id = :challenge_id")
                ->setParameter("challenge_id", $challenge_id)
                ->getQuery()
                ->getResult();
    }
    
    public function getIdAndTitles($ids) {
        if(empty($ids)) {
            $ids = [0];
        }
        $qb = $this->createQueryBuilder("c");
        $challenges = $qb
                ->select("c.id, c.title")
                ->where($qb->expr()->in("c.id", $ids))
                ->getQuery()
                ->getResult();
        return $challenges;
    }
}
