<?php
namespace App\Lib;

use App\Models\User;


use PDO;
use PDOException;
use SessionHandlerInterface;
/*
 * session class
 */

/**
 * @property PDO $dbConnection
 */

class Session implements SessionHandlerInterface{

    private  $user  = false;
    /**
     * @var
     */
    public static  $dbConnection;


    /**
     *
     */
    public function __construct(){

        session_set_save_handler($this, true);
        session_start();
        if(isset($_SESSION['user'])){
            $this->user = $_SESSION['user'];
        }
    }

    /**
     * @param string $path
     * @param string $name
     * @return bool
     */
    public function open(string $path,string $name):bool{
        try{
            self::$dbConnection = new PDO("mysql:host=".DB_HOST
                .";dbname=".DB_NAME,DB_USER,DB_PASSWORD);
            self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e){
            Logger::getLogger()->critical("could not create DB connection: ",['exception' =>$e]);
            die();
        }
        if(isset(self::$dbConnection)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool
     */
    public function close() :bool
    {
        // TODO: Implement close() method.
        self::$dbConnection = null;
        return true;
    }

    /**
     * @param string $id
     * @return string|false
     */
    public function read(string $id) : string|false
    {
        // TODO: Implement read() method.
        try{
            $sql = "SELECT data FROM `sessions` WHERE id=:id";
            $statement = self::$dbConnection->prepare($sql);
            $statement->execute(compact("id"));
            if($statement->rowCount() == 1){
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                return $result['data'];
            }else{
                return "";
            }
        }catch(PDOException $e){
            Logger::getLogger()->critical("could not execute query: ", ['exception'=>$e]);
            die();
        }
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write(string $id, string $data): bool
    {
        // TODO: Implement write() method.
        try{
            $sql = "REPLACE INTO `sessions` (id, data) VALUES(:id, :data)";
            $statement = self::$dbConnection->prepare($sql);
            return $statement->execute(compact("id","data"));
        }catch(PDOException $e){
            Logger::getLogger()->critical("Could not execute query: ", ['exception' =>$e]);
            die();
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function destroy(string $id): bool
    {
        // TODO: Implement destroy() method.
        try{
            $sql= "DELETE FROM `sessions` WHERE id=:id";
            $statement = self::$dbConnection->prepare($sql);
            return $statement->execute(compact("id"));
        }catch(PDOException $e){
            Logger::getLogger()->critical("Could not execute the query: ",["Exception"=>$e]);
            die();
        }
    }

    /**
     * @param int $max_lifetime
     * @return int|false
     */
    public function gc(int $max_lifetime): int|false
    {
        // TODO: Implement gc() method.
        try{
            $sql = "DELETE FROM `sessions` WHERE DATE_ADD(last_accessed, INTERVAL $max_lifetime SECOND)<NOW()";
            $statement = self::$dbConnection->prepare($sql);
            $result = $statement->execute();
            return $result ? $statement->rowCount() :false;
        }catch(PDOException $e){
            Logger::getLogger()->critical("Could not execute query: ", ['exception'=>$e]);
            die();
        }
    }

    /**
     *
     */
    public function __destruct(){
        session_write_close();

    }

    /**
     * @return mixed
     */
    public function isLoggedIn(){
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * @param User $userObj
     * @return bool
     */
    public function login(User $userObj):bool{
        $this->user = $userObj;
        $_SESSION['user'] = $userObj;
        return true;
    }

    /**
     * @return bool
     */
    public function logout() : bool{
        $this->user = false;
        $_SESSION[] = [];
        session_destroy();
        return true;
    }

}