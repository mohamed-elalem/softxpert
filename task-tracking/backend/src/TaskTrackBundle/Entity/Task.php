<?php

namespace TaskTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 */
class Task
{
    /**
     * @var int
     */
    private $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
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
     * @var \TaskTrackBundle\Entity\User
     */
    private $user;

    /**
     * @var \TaskTrackBundle\Entity\Challenge
     */
    private $challenge;


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
     * @var boolean
     */
    private $done;


    /**
     * Set done
     *
     * @param boolean $done
     *
     * @return Task
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setTimeStamps()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateTime()
    {
        $this->setUpdatedAt(new \DateTime());
    }
    /**
     * @var \TaskTrackBundle\Entity\User
     */
    private $supervisor;


    /**
     * Set supervisor
     *
     * @param \TaskTrackBundle\Entity\User $supervisor
     *
     * @return Task
     */
    public function setSupervisor(\TaskTrackBundle\Entity\User $supervisor = null)
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    /**
     * Get supervisor
     *
     * @return \TaskTrackBundle\Entity\User
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }
    /**
     * @var boolean
     */
    private $in_progress;


    /**
     * Set inProgress
     *
     * @param boolean $inProgress
     *
     * @return Task
     */
    public function setInProgress($inProgress)
    {
        $this->in_progress = $inProgress;

        return $this;
    }

    /**
     * Get inProgress
     *
     * @return boolean
     */
    public function getInProgress()
    {
        return $this->in_progress;
    }
}
