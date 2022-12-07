<?php
namespace App\Models;

use App\Lib\Model;

class Bid extends Model {
    protected static $table_name = "bids";

    protected $id = 0;
    protected $item_id;
    protected $amount;
    protected $user_id;

    /**
     * @param $item_id
     * @param $amount
     * @param $user_id
     */
    public function __construct($item_id, $amount, $user_id){
    $this->item_id = $item_id;
    $this->amount = $amount;
    $this->user_id = $user_id;
    }
}