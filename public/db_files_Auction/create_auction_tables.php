<!DOCTYPE html>
<html>
<head>
	<title>Create Auction tables</title>
</head>
<body>
<?php
// Set the variables for the database access:
require_once(__DIR__ . "/../../app/bootstrap.php");
echo "<h1>Table Refresh Script</h1>";

if(isset($_POST['refresh'])) {
	$dbc = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$queries[] = "DROP TABLE IF EXISTS `bids`;";
	$queries[] = "DROP TABLE IF EXISTS `categories`;";
	$queries[] = "DROP TABLE IF EXISTS `images`;";
	$queries[] = "DROP TABLE IF EXISTS `items`;";
	$queries[] = "DROP TABLE IF EXISTS `payments`;";
	$queries[] = "DROP TABLE IF EXISTS `sessions`;";
	$queries[] = "DROP TABLE IF EXISTS `users`;";

	$queries[] = "CREATE TABLE `bids` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT UNSIGNED,
  `amount` DECIMAL(13,2),
  `user_id` INT UNSIGNED
);";

	$queries[] = "CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cat` VARCHAR(20)
);";

	$queries[] = "CREATE TABLE `images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT UNSIGNED,
  `name` VARCHAR(100)
);";

	$queries[] = "CREATE TABLE `items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED,
  `cat_id` INT UNSIGNED,
  `name` VARCHAR(100),
  `price` DECIMAL(13,2),
  `description` text,
  `date` datetime,
  `notified` TINYINT
);";

	$queries[] = "CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `txn_id` VARCHAR(20),
  `mc_gross` DECIMAL(13,2),
  `payment_status` VARCHAR(25),
  `item_number` VARCHAR(25),
  `item_name` VARCHAR(255),
  `payer_id` VARCHAR(50),
  `payer_email` VARCHAR(255),
  `full_name` VARCHAR(255),
  `address_street` VARCHAR(255),
  `address_city` VARCHAR(255),
  `address_state` VARCHAR(255),
  `address_zip` VARCHAR(20),
  `address_country` VARCHAR(255),
  `payment_date` VARCHAR(255)
);";

	$queries[] = "CREATE TABLE `sessions` (
  `id` char(32) NOT NULL PRIMARY KEY,
  `data` MEDIUMTEXT,
  `last_accessed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);";

	$queries[] = "CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50),
  `password` VARCHAR(255),
  `email` VARCHAR(100),
  `verify` VARCHAR(20),
  `active` TINYINT
);";

	try {
		foreach($queries as $query) {
			$dbc->query($query);
		}
	} catch(PDOException $exception) {
		echo "Error: " . $exception->getMessage() . "<br>";
	}

	echo "Tables successfully refreshed";

	$dbc = null;
} else {
	?>
	<p>Would you like to refresh the Auction tables (<i>bids</i>, <i>categories</i>, <i>images</i>, <i>items</i>, <i>payments</i>,
		<i>sessions</i>, <i>users</i>)?<p>
	<p><b>Warning: existing tables and records will be destroyed!</b></p>
	<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
		<input type="submit" name="refresh" value="Yes">
	</form>
	<?php
}
?>
</body>
</html>