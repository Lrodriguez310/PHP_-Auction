<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
<?php
// Set the variables for the database access:
require_once(__DIR__ . "/../../app/bootstrap.php");

$dbc = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

if(isset($_POST['insert'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$enc_pass = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
	$email = trim($_POST['email']);

	$query = "INSERT into `users` values ('0', '$username', '$enc_pass', '$email', '', 1)";
	$dbc->query($query);

} elseif(isset($_POST['delete'])) {
	$id = trim($_POST['id']);

	$query = "DELETE FROM `users` WHERE id = '$id'";
	$dbc->query($query);

}

?>

<h2 style="text-align: center">Manage Users</h2>
<table border="1" width="75%" cellspacing="2" cellpadding="2" align="center">
    <tr>
        <th>ID (auto_increment)</th>
        <th>Username</th>
        <th>Password</th>
	    <th>Email</th>
	    <th>Verify</th>
	    <th>Active</th>
        <th>Delete?</th>
    </tr>

	<?php
	$query = "SELECT * from `users`  ORDER BY id";
	$users = $dbc->query($query)->fetchAll(PDO::FETCH_ASSOC);

	foreach($users as $row) {
		?>
        <tr align="center" valign="top">
            <td align="center" valign="top"><?php echo $row['id'] ?></td>
            <td align="center" valign="top"><?php echo $row['username'] ?></td>
            <td align="center" valign="top"><?php echo $row['password'] ?></td>
	        <td align="center" valign="top"><?php echo $row['email'] ?></td>
	        <td align="center" valign="top"><?php echo $row['verify'] ?></td>
	        <td align="center" valign="top"><?php echo $row['active'] ?></td>
            <td align="center" valign="top">
                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
                    <input type='hidden' name="id" value="<?php echo $row['id'] ?>">
                    <input type="submit" name="delete" value="X">
                </form>
            </td>

        </tr>
		<?php
	}
	?>
</table>

<h2>Insert a new user:</h2>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
    Username:<input type="text" name="username" size="10"><br>
    Password:<input type="text" name="password" size="10"><br>
	Email:<input type="email" name="email" size="10"><br>
    <input type="submit" name="insert" value="Insert">
</form>

<?php
$dbc = null;
?>
</body>
</html>