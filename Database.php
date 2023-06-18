<?php

class Database
{
    private $db;

    public function __construct(){}

    public function __destruct()
    {
        $this->db->close();
    }

    public function connectDB()
    {
        try
        {
            $this->db = new mysqli("localhost", "root", "", "fruits");

        }
        catch (mysqli_sql_exception $e) {
            trigger_error("Could not connect to database: " . $e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }
}