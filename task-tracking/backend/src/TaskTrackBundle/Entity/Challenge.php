<?php

namespace TaskTrackBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Challenge
 */
class Challenge
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tasks;

    /**
     * @var \TaskTrackBundle\Entity\Challenge
     */
    private $parent;

    /**
     * @var \TaskTrackBundle\Entity\User
     */
    private $supervisor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set duration
     *
     * @param integer $duration
     *
     * @return Challenge
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Challenge
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Challenge
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
     * @return Challenge
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
     * Add child
     *
     * @param \TaskTrackBundle\Entity\Challenge $child
     *
     * @return Challenge
     */
    public function addChild(\TaskTrackBundle\Entity\Challenge $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \TaskTrackBundle\Entity\Challenge $child
     */
    public function removeChild(\TaskTrackBundle\Entity\Challenge $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add task
     *
     * @param \TaskTrackBundle\Entity\Task $task
     *
     * @return Challenge
     */
    public function addTask(\TaskTrackBundle\Entity\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \TaskTrackBundle\Entity\Task $task
     */
    public function removeTask(\TaskTrackBundle\Entity\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set parent
     *
     * @param \TaskTrackBundle\Entity\Challenge $parent
     *
     * @return Challenge
     */
    public function setParent(\TaskTrackBundle\Entity\Challenge $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \TaskTrackBundle\Entity\Challenge
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set supervisor
     *
     * @param \TaskTrackBundle\Entity\User $supervisor
     *
     * @return Challenge
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
