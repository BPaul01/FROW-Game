<?php

namespace entities;
class QuestionEntity
{
    private $id;
    private $fruitId;
    private $level;
    private $question;
    private $photo;


    //public function __construct(){}

    /**
     * @param $id
     * @param $fruitId
     * @param $level
     * @param $question
     * @param $photo
     */
    public function __construct($id, $fruitId, $level, $question, $photo)
    {
        $this->id = $id;
        $this->fruitId = $fruitId;
        $this->level = $level;
        $this->question = $question;
        $this->photo = $photo;
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
    public function getFruitId()
    {
        return $this->fruitId;
    }

    /**
     * @param mixed $fruitId
     */
    public function setFruitId($fruitId)
    {
        $this->fruitId = $fruitId;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }
}