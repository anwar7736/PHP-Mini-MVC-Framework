<?php

namespace Config;

use App\Models\Model;
use App\Models\Post;
use PDO;
class Database{
    public $connection;
    public $statement;
    public function __construct($config, $user, $pass)
    {
        $dsn = "mysql:".http_build_query($config, "", ";");
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->connection = new PDO($dsn, $user, $pass, $options);
    }

    public function raw($query, $params = [])
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        Model::destroyInstance();
        return $this->statement;
    }    
    
    public function query($query, $params = [])
    {
        $this->raw($query, $params);
        return $this;
    }

    public function get()
    {
        Model::destroyInstance();
        return $this->statement->fetchAll();
    }    
    
    public function find()
    {
        Model::destroyInstance();
        return $this->statement->fetch();
    }    
    
    public function findOrFail()
    {
        $result = $this->find();
        if(!$result)
        {
            return abort(Response::NOT_FOUND);
        }
        Model::destroyInstance();
        return $result;
    }
    
}