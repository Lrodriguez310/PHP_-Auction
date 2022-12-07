<?php
namespace App\Lib;

use PDO;
use PDOException;
use ReflectionClass;
use ReflectionException;

class Database{
    private static $databaseObj;
    private  $connection;

    /**
     * @return Database
     */
    public static function getConnection() : Database{
        if(!self::$databaseObj)
            self::$databaseObj = new self();
        return self::$databaseObj;
    }

    /**
     *
     */
    private function __construct()
    {
        try{
            $this->connection = new PDO("mysql:host=".DB_HOST.
            ";dbname=".DB_NAME,DB_USER,DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE,
                                            PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            Logger::getLogger()->critical("Could not create DB connection: ", ['exception'=>$e]);
            die();
        }

    }

    /**
     *
     */
    public function __destruct()
    {
        $this->connection= null;

        // TODO: Implement __destruct() method.
    }

    /**
     * @param string $sql
     * @param $bindVal
     * @param bool $retStmt
     * @return bool|\PDOStatement|void
     */
    public function sqlQuery(string $sql, $bindVal = null, bool $retStmt = false){
        try{
            $statement = $this->connection->prepare($sql);
            if (is_array($bindVal)){
                $result = $statement->execute($bindVal);
            }else{
                $result = $statement->execute();
            }
            if($retStmt){
                return $statement;
            }else{
                return $result;
            }
        }catch(PDOException $e){
            Logger::getLogger()->critical("could not execute the query: ", ['exception'=>$e]);
            die();
        }
    }

    /**
     * @param string $sql
     * @param string $class
     * @param $bindVal
     * @return array
     */
    public function fetch(string $sql, string $class, $bindVal = null):array{
    $statement = $this->sqlQuery($sql, $bindVal,true);
    if($statement->rowCount()==0){
        return [];
    }
    try{
        $reflect = new ReflectionClass($class);
        if($reflect->getConstructor()==null){
            $ctor_args = [];
        }else{
            $num = count($reflect->getConstructor()->getParameters());
            $ctor_args = array_fill(0,$num,null);
        }
        return $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
                                            $class, $ctor_args);
    }catch(ReflectionException $e){
        Logger::getLogger()->critical("Reflection error: ", ['exception' => $e]);
        die();
    }
    }

    /**
     * @return string
     */
    public function lastInsertId():string{
        $id = $this->connection->lastInsertId();
        return $id;
    }

    /**
     * @param string $sql
     * @param $bindVal
     * @return int
     */
    public function rowCount(string $sql, $bindVal = null):int {
        $statement = $this->sqlQuery($sql, $bindVal, true);
        return $statement->rowCount();
    }
}
