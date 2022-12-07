<?php

namespace App\Lib;

use App\Exceptions\ClassException;
use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class Model
{
    use Helper;
    public static function find($cond = null, string $groupBy = null, string $orderBy = null, $limit = null): array
    {
        $db = Database::getConnection();
        $sql = "SELECT * FROM `".static::$table_name."`";
        if (is_array($cond)) {
            $sql .=" WHERE ";
            $bindings = [];
            foreach ($cond as $key => $value) {
                $bindings[] = "`$key` = :$key";
            }
            $sql .= implode(" AND ", $bindings);
        } else if ($cond != null){
            $sql .= " WHERE $cond";
        }
        if (isset($groupBy)){
            $sql .= " GROUP BY $groupBy";
        }
        if (isset($orderBy)){
            $sql .= " ORDER BY $orderBy";
        }
        if (isset($limit)){
            $sql .= " LIMIT $limit";
        }
        $results = $db->fetch($sql, get_called_class(), $cond);
        return $results;
    }

    /**
     * @throws ClassException
     */
    public static function findFirst($cond = null, string $groupBy = null)
    {
        $objs = static::find($cond, $groupBy, null, 1);
        if(empty($objs)) throw new ClassException("Model not found");
        return array_shift($objs);
    }

    /**
     * @throws ClassException
     */
    public static function all(string $groupBy = null, string $orderBy = null): array {
        $objs = static::find(null, $groupBy, $orderBy, null);
        if(empty($objs)) throw new ClassException("Model not found");
        return $objs;
    }

    public function create($cond = []) {
        if(!empty($cond)){
            $condObj = static::find($cond);
            if(!empty($condObj)){
                return false;
            }
        }
        $newId = $this->insert();
        $this->id = $newId;
        return $this;
    }

    private function getColumnNames(): array {
        $db = Database::getConnection();
        $table_data = ($db->sqlQuery("DESCRIBE " . static::$table_name, null, true))->fetchAll(PDO::FETCH_ASSOC);
        $table_props = array_map(function ($a) {
            return $a['Field'];
        }, $table_data);
        try {
            $reflect = new ReflectionClass(get_called_class());
            $reflectProps = $reflect->getProperties();
            $props = array_column(array_map(function($a){
                $a->setAccessible(true);
                return [$a->getName(), $a->getValue($this)];
            }, $reflectProps), 1, 0);
        } catch (ReflectionException $e){
            Logger::getLogger()->critical("Reflection error: ", ['exception' =>$e]);
            die();
        }
        $names = array_intersect_key($props, array_flip($table_props));
        return $names;
    }
    private function insert(): string {
        $db = Database::getConnection();
        $bindVals = static::getColumnNames();
        $sql = "INSERT INTO `" . static::$table_name . "`";
        $sql .="(";
        $sql .= implode(", ", array_keys($bindVals));
        $sql .=") VALUES (";
        $bindings = [];
        foreach(array_keys($bindVals) as $key) {
            $bindings[] = ":$key";
        }
        $sql .= implode(", ", $bindings);
        $sql .=")";
        $db->sqlQuery($sql, $bindVals);
        $lastId = $db->lastInsertId();
        return $lastId;
    }
    public function delete(){
        $db = Database::getConnection();
        $sql = "DELETE FROM `" . static::$table_name . "`";
        $sql .= " WHERE id = :id";
        $bindVal = ['id' => $this->id];
        $result = $db->sqlQuery($sql, $bindVal);
        return $result;
    }

    public function update() {
        $db = Database::getConnection();
        $bindVals = static::getColumnNames();
        $sql = "UPDATE `" . static::$table_name . "`";
        $sql .= " SET ";
        $bindings = [];
        foreach(array_keys($bindVals) as $key){
            $bindings [] = "`$key` = :$key";
        }
        $sql .= implode(", ", $bindings);
        $sql .= " WHERE `id` = :id";
        $result = $db->sqlQuery($sql, $bindVals);
        return $result;
    }

}