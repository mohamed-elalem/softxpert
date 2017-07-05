<?php

namespace TaskTrackBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Task
 */
class Task
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var float
     */
    private $score;

    /**
     * @var integer
     */
    private $seconds;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \TaskTrackBundle\Entity\Challenge
     */
    private $challenge;
    
    /**
     * @var \TaskTrackBundle\Entity\User
     */
    private $user;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set score
     *
     * @param float $score
     *
     * @return Task
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set seconds
     *
     * @param integer $seconds
     *
     * @return Task
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;

        return $this;
    }

    /**
     * Get seconds
     *
     * @return integer
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Task
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Task
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set challenge
     *
     * @param \TaskTrackBundle\Entity\Challenge $challenge
     *
     * @return Task
     */
    public function setChallenge(\TaskTrackBundle\Entity\Challenge $challenge = null)
    {
        $this->challenge = $challenge;

        return $this;
    }

    /**
     * Get challenge
     *
     * @return \TaskTrackBundle\Entity\Challenge
     */
    public function getChallenge()
    {
        return $this->challenge;
    }
    
    
    /**
     * Set user
     *
     * @param \TaskTrackBundle\Entity\User $user
     *
     * @return Task
     */
    public function setUser(\TaskTrackBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TaskTrackBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setTimeStamps()
    {
        $this->created_at = new \DateTime();
    
        $this->updated_at = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateTime()
    {
        $this->updated_at = new \DateTime();
    }


}
