<?php
require_once(__DIR__ . "/../app/bootstrap.php");

use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\Item as Product;
use App\Models\User;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;


$validid = pf_validate_number($_GET['id'], "redirect", CONFIG_URL);
try {
	$product = Product::findFirst(["id" => $validid]);
} catch(ClassException $e) {
	Logger::getLogger()->critical("Invalid Product: ", ['exception' => $e]);
	echo "Invalid Product";
	die();
}

if(!$product) {
	echo "Error retrieving item details!";
	die();
}

$product->getBids();

$item_name = $product->get('name');

$temp = $product->get('bidObjs');
$itemWinnerBidObj = array_shift($temp);
$item_amount = $itemWinnerBidObj->get("amount");
try {
	$itemOwnerObj = User::findFirst(["id" => $product->get("user_id")]);
} catch(ClassException $e) {
	Logger::getLogger()->critical("Invalid User: ", ['exception' => $e]);
	echo "Invalid User";
	die();
}

//Create new Order Request
$request = new OrdersCreateRequest();
$request->prefer('return=representation');
//Set intent, purchase details
$request->body = [
	"intent"              => "CAPTURE",
	"purchase_units"      =>
		[
			[
				//A custom application reference id
				"reference_id" => uniqid(),
				"amount"       => [
					//This value must match the breakdown total
					"value"         => round($item_amount * 1.13, 2),
					"currency_code" => PAYPAL_CURRENCY,
					"breakdown"     =>
						[
							"item_total" =>
								[
									"currency_code" => PAYPAL_CURRENCY,
									"value"         => $item_amount
								],
							"tax_total"  =>
								[
									"currency_code" => PAYPAL_CURRENCY,
									"value"         => round($item_amount * 0.13, 2),
								],
						]
				],
				//Specify item details
				"items"        =>
					[
						[
							"name"        => $item_name,
							"description" => $item_name,
							"sku"         => $product->get('id'),
							"unit_amount" =>
								[
									'currency_code' => PAYPAL_CURRENCY,
									'value'         => $item_amount,
								],
							"tax"         =>
								[
									'currency_code' => PAYPAL_CURRENCY,
									'value'         => round($item_amount * 0.13, 2),
								],
							"quantity"    => '1',
							"category"    => 'PHYSICAL_GOODS',
						]
					],
			]],
	//Set return & cancel callback addresses
	"application_context" =>
		[
			"cancel_url" => PAYPAL_CANCELURL,
			"return_url" => PAYPAL_RETURNURL
		]
];

try {
	// Call API with your client and get a response for your call
	$response = $client->execute($request);

	//var_dump($response); die(); //View all return values
	if ($response->result->links[1]->rel == "approve") {
		$approveUrl =  $response->result->links[1]->href;
		header('Location: ' . $approveUrl);
	} else {
		error_log("Error occured");
	}

} catch(HttpException $ex) {
	echo $ex->statusCode;
	print_r($ex->getMessage());
}

die();