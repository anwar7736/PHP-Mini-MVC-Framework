<?php

namespace Config;

use App\Models\Model;
use App\Models\Post;
use Exception;
use PDO;
class Database{
    public $connection;
    public $statement;
    public function __construct($config, $user, $pass)
    {
        $driver = $config['driver'];

        switch ($driver) {
            case 'mysql':
                $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
                break;

            case 'pgsql':
                $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
                break;

            case 'sqlite':
                $dsn = "sqlite:{$config['database']}";
                break;

            case 'sqlsrv':
                $dsn = "sqlsrv:Server={$config['host']};Database={$config['database']}";
                break;

            default:
                throw new Exception("Unsupported driver: $driver");
        }

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
    
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
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