<?php

class UserEntity
{
    private $id;
    private $userName;
    private $isAdmin;

    /**
     * @param $id
     * @param $userName
     */
    public function __construct($id, $userName, $isAdmin)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}