<?php
require_once(__DIR__ . "/../app/bootstrap.php");
use App\Lib\Logger;
use App\Models\Payment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
$approvedOrderId = $_GET['token'];
// OrdersCaptureRequest() creates a POST request to

$request = new OrdersCaptureRequest($approvedOrderId);
$request->prefer('return=representation');

try {
// Call API with your client and get a response for your call
    $response = $client->execute($request);
//print_r($response);die(); //To view all available fields
//Item info
    $item_number = $response->result->purchase_units[0] ->items[0]->sku;
$item_name = $response->result->purchase_units[0]->items[0] ->name;
//Transaction info
$paidTo = $response->result->purchase_units[0]->payee ->email_address;
$payment_gross = $response->result->purchase_units[0] ->payments->captures[0]->amount->value;
$currency_code = $response->result->purchase_units[0] ->payments->captures[0]->amount->currency_code;
$payment_status = $response->result->purchase_units[0] ->payments->captures[0]->status;
$txn_id = $response->result->purchase_units[0]->payments ->captures[0]->id;
$date_obj = DateTime::createFromFormat('Y-m-d\TH:i:s\Z',
    $response->result->create_time);
$payment_date = $date_obj->format("Y-m-d H:i:s");
//Payer Info
$payer_id = $response->result->payer->payer_id;
$payer = $response->result->payer->email_address;
//Shipping info
$full_name = $response->result->purchase_units[0]->shipping ->name->full_name;
$addressStreet = $response->result->purchase_units[0] ->shipping->address->address_line_1;
$addressCity = $response->result->purchase_units[0] ->shipping->address->admin_area_2;
$addressProvince = $response->result->purchase_units[0] ->shipping->address->admin_area_1;
$addressPostal = $response->result->purchase_units[0] ->shipping->address->postal_code;
$addressCountry = $response->result->purchase_units[0] ->shipping->address->country_code;
}catch(Exception $e) {
    Logger::getLogger()->error("PayPal: Transaction failed
to execute ", ['exception' => $e]);
    die();
};
$paymentObj = new Payment(
    $txn_id,
    $payment_gross,
    $payment_status,
    $item_number,
    $item_name,
    $payer_id,
    $payer,
    $full_name,
    $addressStreet,
    $addressCity,
    $addressProvince,
    $addressPostal,
    $addressCountry,
    $payment_date);
//Insert payment into db
$result = $paymentObj->create();
if($result) {
    Logger::getLogger()->debug("PayPal: Payment has been
made & successfully inserted into the Database");
} else {
    Logger::getLogger()->alert("PayPal: Error inserting
into DB ");
};
require(__DIR__ . "/../app/Layouts/header.php");
echo <<<RECEIPTPAYMENT_
<h1>Receipt of Payment</h1>
<table cellpadding='5'>
<tr> <td>Item Name: </td> <td>$item_name</td> </tr>
<tr> <td>Amount Paid: </td> <td>$payment_gross
$currency_code</td> </tr>
<tr> <td>Shipping address: </td> <td>
$full_name<br>
$addressStreet<br>
$addressCity $addressProvince $addressPostal<br>
$addressCountry<br>
</td></tr>
<tr> <td>Paid to: </td><td>$paidTo</td> </tr>
<tr> <td>Payment Status: </td><td>$payment_status</td>
</tr>
<tr> <td>Transaction ID: </td><td>$txn_id</td> </tr>
</table>
<p>Your payment was successful.<br>Thank you for your
business!</p>
RECEIPTPAYMENT_;

require(__DIR__ . "/../app/Layouts/footer.php");
