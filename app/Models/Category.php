<?php
namespace App\Models;
use App\Lib\Model;
class Category extends Model{
    protected static $table_name="categories";

    protected $id = 0;
    protected $cat;

    /**
     * @param $cat
     */
    public function __construct($cat){
    $this->cat = $cat;
    }
}