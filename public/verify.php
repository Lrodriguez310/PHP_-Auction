<?php

use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\User;

require_once(__DIR__ . "/../app/bootstrap.php");

require(__DIR__ . "/../app/Layouts/header.php");

$verify = addslashes(urldecode($_GET['verify']));
$verifyemail = addslashes(urldecode($_GET['email']));

try {
	// (A) Check if the given email and verify string matches the record in the table
	$user = User::findFirst(['email'=>$verifyemail, "verify"=>$verify]);
} catch(ClassException $e) {
	Logger::getLogger()->critical("Invalid User: ", ['exception' => $e]);
	echo "Invalid User";
	die();

}

if($user) {
	// (B) If there was a match, set the active property to '1' and update the database
    $user->set("active", 1);
    $result = $user->update();

	if($result) {
		echo "Your account has now been verified. You can now <a href='login.php'>log in</a>";
	} else {
		echo "Update failed!";
	}
} else {
	echo "This account could not be verified.";
}

echo " Verification value:" . $verify;

require(__DIR__ . "/../app/Layouts/footer.php");