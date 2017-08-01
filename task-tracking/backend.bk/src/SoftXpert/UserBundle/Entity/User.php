<?php

namespace SoftXpert\UserBundle\Entity;

/**
 * User
 */
class User
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

    private $name;
    private $email;
    private $password;
    private $role;
    private $created_at;
    private $updated_at;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }
    
    public function setCreatedAt() {
        $this->created_at = new \DateTime();
        return $this;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }
    
    public function setUpdatedAt() {
        $this->updated_at = new \DateTime();
        return $this;
    }
    
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set role
     *
     * @param integer $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer
     */
    public function getRole()
    {
        return $this->role;
    }
}
