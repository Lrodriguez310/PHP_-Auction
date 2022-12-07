<?php

use App\Exceptions\ClassException;
use App\Exceptions\MailException;
use App\Lib\Logger;
use App\Models\Item;
use App\Lib\Mail;
use App\Models\User;

require_once(__DIR__ . "/../app/bootstrap.php");

require(__DIR__ . "/../app/Layouts/header.php");
echo "<h2>Process Auction Bids</h2>";

//Find (from the database) all auctions that have ended and have not yet been notified. Return all items in an array of item objects
$items = Item::find("date < NOW() AND notified = 0");

/* @var $item \App\Models\Item */
foreach($items as $item) {

	$currency = CONFIG_CURRENCY;
	//Load Bid objects into item
	$item->getBids();
	//Get the item's owner user object
	try {
		$itemOwner = User::findFirst(["id" => $item->get("user_id")]);
	} catch(ClassException $e) {
		Logger::getLogger()->critical("Invalid User: ", ['exception' => $e]);
		echo "Invalid User";
		die();
	}

	$ownerName = $itemOwner->get('username');
	$ownerEmail = $itemOwner->get('email');

	$itemName = $item->get('name');

	if(count($item->get("bidObjs")) == 0) {

		$mail_body = <<<_OWNER_
Hi $ownerName,

Sorry, but your item '$itemName', did not have any bids placed with it.

_OWNER_;

		//Call the sendmail static function of the mail class to send the email to the item owner
		try {
			Mail::sendMail($ownerEmail, "Your item '" . $itemName . "' did not sell", $mail_body);
		} catch(MailException $e) {
			Logger::getLogger()->critical("could not send mail: ", ['exception' => $e]);
		}

		echo nl2br($mail_body);

	} else {

		//Get the item's winner bid object
		/* @var $winnerBid \App\Models\Bid[] */
		$winnerBid = $item->get("bidObjs");
		$winnerBid = array_shift($winnerBid);
		$winnerAmt = $winnerBid->get("amount");

		//Get the item's winner user object
		/* @var $winnerUser \App\Models\User */
		try {
			$winnerUser = User::findFirst(["id" => $winnerBid->get("user_id")]);
		} catch(ClassException $e) {
			Logger::getLogger()->critical("Invalid User: ", ['exception' => $e]);
			echo "Invalid User";
			die();
		}

		$winnerName = $winnerUser->get("username");
		$winnerEmail = $winnerUser->get("email");

// (A) Create body of email to Item owner about winning auction
        $owner_body = <<<_OWNER_
Hi $ownerName,

Congratulations! The auction for your item '$itemName' , has completed with a winning bid of
$currency $winnerAmt bidded by {$winnerName}!

Bid details:

Item: $itemName
Amount: $currency $winnerAmt
Winning bidder: $winnerName ({$winnerEmail})

It is recommended that you contact the winning bidder within 3 days

_OWNER_;

		$winner_body = <<<_WINNER_
Hi $winnerName, 
 
Congratulations! Your bid of $currency $winnerAmt for 
the item '$itemName' was the highest bid! 
 
Bid details: 
 
Item: $itemName 
Amount: $currency $winnerAmt 
Owner: $ownerName ($ownerEmail)  

Click here to pay for the item:
{$generateButton($item->get('id'))}


It is recommended that you contact the owner of the item within 3 days.
_WINNER_;

		$owner_body = nl2br($owner_body);
		$winner_body = nl2br($winner_body);

		//Call the sendmail static function of the mail class to send the email to the item owner and winner
		try {
			Mail::sendMail($ownerEmail, "Your item '" . $itemName . "' has sold", $owner_body);
		} catch(MailException $e) {
			Logger::getLogger()->critical("could not send owner mail: ", ['exception' => $e]);
		}

		try {
			Mail::sendMail($winnerEmail, "You won item '" . $itemName . "'!", $winner_body);
		} catch(MailException $e) {
			Logger::getLogger()->critical("could not send winner mail: ", ['exception' => $e]);
		}

		echo "<h3>TO OWNER:</h3>";
		echo $owner_body;

		echo "<br><h3>TO WINNER:</h3>";
		echo $winner_body;
	}

	// (B) Update the notified property for the item and update the database
    $item->set("notified", 1);
    $item->update();

}

require(__DIR__ . "/../app/Layouts/footer.php");
