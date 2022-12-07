<?php

use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;

require_once(__DIR__.'/../app/bootstrap.php');

$validid = pf_validate_number($_GET['id'], "redirect", CONFIG_URL);
try {
    $item = Item::findFirst(["id" => $validid]);
} catch(ClassException $e) {
    Logger::getLogger()->critical("Invalid Item: ", ['exception' => $e]);
    echo "Invalid Item";
    die();
}
$item->getImages();
if(!$item->get('imageObjs')) {
    echo "No Image";
} else {
    $img = $item->get('imageObjs');
    $firstImg = array_shift($img);
}

$item->getBids();



if(isset($_POST['submit'])) {
    if(is_numeric($_POST['bid']) == false) {
        header("Location: itemdetails.php?id=" . $validid . " &error=letter");
        die();
    }
    $validbid = false;
    if(count($item->get('bidObjs')) == 0) {
        $price = intval($item->get('price'));
        $postedBid = intval($_POST['bid']);

        if ($postedBid >= $price) {
            $validbid = true;
        }
    }else {
        $bids = $item->get('bidObjs');
        $highestBid = array_shift($bids);
        $highestBid = intval($highestBid->get('amount'));
        $postedBid = intval($_POST['bid']);
        if ($postedBid > $highestBid) {
            $validbid = true;
        }
    }
    if($validbid == false){
        header("Location: itemdetails.php?id=" . $validid . "error=lowprice#bidbox");
        die();
    } else {
        $newBid = New Bid($item->get('id'), $_POST['bid'], $session->getUser()->get('id'));
        $newBid->create();
        header("Location: itemdetails.php?id=" . $validid);
        die();
    }
}
require(__DIR__.'/../app/Layouts/header.php');
$nowepoch = time();
$itemepoch = strtotime($item->get('date'));
$validAuction = false;
if($itemepoch > $nowepoch) {
    $validAuction = true;
}
?>
<h1> <?php echo $item->get('name')?></h1>
<?php
echo "<p>";
/* Check if there are bids for item */
if(!$item->get('bidObjs')) {
    echo "<br/><strong>This Item Has no bids - Starting Price: ".CONFIG_CURRENCY ."</strong>" . sprintf('%.2f', $item->get('price'));
} else {
    $bids = $item->get('bidObjs');
    $highestBid = array_shift($bids);
    echo "<strong>Number of Bids </strong>: " . count($item->get('bidObjs')) . " - <strong>Current Price</strong>: " . CONFIG_CURRENCY . sprintf('%.2f', $highestBid->get('amount'));
}
echo "- <strong>Auction Ends</strong>: " . date("D jS F Y g.iA", $itemepoch);
echo "</p>";

/* Check if there is an image for item */
if(!$item->get('imageObjs')) {
    echo "<br/><strong>This Item Has no Photo.</strong>";
} else {
    echo "<br/><img width='200' src='imgs/".$firstImg->get('name') . "'>";
}

/* Display Description */
echo "<br><p>" . $item->get('description'). "</p>";
echo "<a name='bidbox'></a>";
echo "<h2>Bid for this item</h2>";

if(!$session->IsLoggedIn()){
    echo "<br/><p>To Bid, you need to log in. <a href='login.php?id=".$validid . "&ref=addbid'>Login Here</a></p>";
} else {
    if($validAuction == true) {
        echo "<p>Enter the bid amount into the box below.</p>";
        echo "<p>";
        if(isset($_GET['error'])) {
            try {
                $errorMsg = Item::displayError($_GET['error']);
            } catch (ClassException $e) {
                Logger::getLogger()->error("Invalid error code: ", ['exception' => $e]);
                die();
            }
            echo $errorMsg;
        }
        ?>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <table>
                <tr>
                    <td><input type="number" name="bid"></td>
                    <td><input type="submit" name="submit" id="submit" value="Bid!"></td>
                </tr>
            </table>
        </form>
        <?php
    }
    else {
        echo "<br>This auction has now ended.";
    }
    if(count($item->get('bidObjs')) > 0) {
        echo "<h2>Bid History</h2>";
        echo "<ul>";
        foreach ($bids as $bid) {
            $id = $bid->get('user_id');
            try {
                $user = User::findFirst(["id" => "$id"]);
            } catch (ClassException $e) {
                Logger::getLogger()->critical("Invalid User: ", ['exception' => $e]);
                echo "<p>Invalid User</p>";
                die();
            }
            echo "<li>{$user->get('username')} - " . CONFIG_CURRENCY . sprintf('%.2f', $bid->get('amount')) . "</li>";
        }
        echo "</ul>";
    }
}
require(__DIR__.'/../app/Layouts/footer.php');
?>