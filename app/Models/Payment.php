<?php


namespace App\Models;


use App\Lib\Model;


class Payment extends Model
{

    protected static $table_name = "payments";


    protected $id = 0;

    protected $txn_id;

    protected $mc_gross;

    protected $payment_status;

    protected $item_number;

    protected $item_name;

    protected $payer_id;

    protected $payer_email;

    protected $full_name;

    protected $address_street;

    protected $address_city;

    protected $address_state;

    protected $address_zip;

    protected $address_country;

    protected $payment_date;


    public static function generatePayment(int $id): string
    {

        $url = CONFIG_URL . "/payment.php?id=$id";

        $PayPalButton = <<<HEREDOC_

<a href='{$url}'>

<img src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="Paypal - The safer, easier way to pay online" border="0">

</a>

HEREDOC_;


        return $PayPalButton;

    }


    public function __construct($txn_id, $mc_gross, $payment_status, $item_number, $item_name

        ,                       $payer_id, $payer_email, $full_name, $address_street, $address_city, $address_state,

                                $address_country, $address_zip, $payment_date

    )

    {

        $this->txn_id = $txn_id;

        $this->mc_gross = $mc_gross;

        $this->payment_status = $payment_status;

        $this->item_number = $item_number;

        $this->item_name = $item_name;

        $this->payer_id = $payer_id;

        $this->payer_email = $payer_email;

        $this->full_name = $full_name;

        $this->address_street = $address_street;

        $this->address_city = $address_city;

        $this->address_state = $address_state;

        $this->address_country = $address_country;

        $this->address_zip = $address_zip;

        $this->payment_date = $payment_date;

    }

}

