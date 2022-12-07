<?php

use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\User;
use App\Lib\Model;

require_once(__DIR__ . "/../app/bootstrap.php");

if(isset($_POST['submit'])) {
	if($_POST['password1'] == $_POST['password2'] && !empty($_POST['password1'])) {

		// (A) Create a new user object and add the user to the database

       $newUser = new User($_POST['username'], $_POST['password1'], $_POST['email']);
       $result = $newUser->create(["email"=>"{$_POST['email']}"]);




		//If boolean false is returned then the username has been taken
		if(!$result) {
			header("Location: register.php?error=taken");
			die();
		}

		// (B) Send an email to the new user's given email address
        $newUser->mailUser();
        require(__DIR__ . "/../app/Layouts/header.php");
		echo "A link has been emailed to the address you entered below. Please follow the link in the email to validate your account.";
	} else {
		//Passwords do not match
		header("Location: register.php?error=pass");
		die();
	}

} else {
	require(__DIR__ . "/../app/Layouts/header.php");
	if(isset($_GET['error'])) {
		try {
			$errorMsg = User::displayError($_GET['error']);
		} catch(ClassException $e) {
			Logger::getLogger()->error("Invalid error code: ", ['exception' => $e]);
			die();
		}
		echo $errorMsg;
	}
	?>
	<h2>Register</h2>
	To register on the <?php echo CONFIG_AUCTIONNAME; ?> , fill in the form below.
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
		<table>
			<tr>
				<td>Email</td>
				<td><input type="text" name="email"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password1"></td>
			</tr>
			<tr>
				<td>Password (again)</td>
				<td><input type="password" name="password2"></td>
			</tr>
			<tr>
				<td>Username</td>
				<td><input type="text" name="username"></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Register!"></td>
			</tr>
		</table>
	</form>

	<?php
}

require(__DIR__ . "/../app/Layouts/footer.php");

?>