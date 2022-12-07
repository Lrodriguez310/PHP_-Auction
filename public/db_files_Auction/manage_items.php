<!DOCTYPE html>
<html>
<head>
	<title>Manage Items</title>
</head>
<body>
<?php
// Set the variables for the database access:
require_once(__DIR__ . "/../../app/bootstrap.php");

$dbc = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

if(isset($_POST['insert'])) {
	$user_id = trim($_POST['user_id']);
	$cat_id = trim($_POST['cat_id']);
	$name = trim($_POST['name']);
	$price = trim($_POST['price']);
	$description = trim($_POST['description']);
	$date = trim($_POST['date']);

	$uploads_dir = __DIR__ . '/../imgs/';
	if(!is_dir($uploads_dir)) {
		mkdir($uploads_dir);
	}
	if($_FILES["image"]["error"] == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["image"]["tmp_name"];
		$filename = basename($_FILES["image"]["name"]);
		move_uploaded_file($tmp_name, $uploads_dir . $filename);
	}

	$query = "INSERT into `items` values ('0', '$user_id', '$cat_id', '$name', '$price', '$description', '$date', '0')";
	$dbc->query($query);

	$item_id = $dbc->lastInsertId();
	$query = "INSERT into `images` values ('0', '$item_id', '$filename')";
	$dbc->query($query);

} elseif(isset($_POST['delete'])) {
	$id = trim($_POST['id']);

	$uploads_dir = __DIR__ . '/../imgs/';
	$query = "SELECT * from `images` WHERE item_id = '$id' LIMIT 1";
	$image = $dbc->query($query)->fetch(PDO::FETCH_ASSOC);

	unlink($uploads_dir . $image['name']);

	$query = "DELETE FROM `images` WHERE item_id = '$id'";
	$dbc->query($query);

	$query = "DELETE FROM `items` WHERE id = '$id'";
	$dbc->query($query);

}

?>

<h2 style="text-align: center">Manage Items</h2>
<table border="1" width="75%" cellspacing="2" cellpadding="2" align="center">
	<tr>
		<th>ID (auto_increment)</th>
		<th>User ID</th>
		<th>Cat ID</th>
		<th>Name</th>
		<th>Price</th>
		<th>Description</th>
		<th>Date</th>
		<th>Notified</th>
		<th>Image</th>
		<th>Delete?</th>
	</tr>

	<?php
	$query = "SELECT * from `items` ORDER BY id";
	$comments = $dbc->query($query)->fetchAll(PDO::FETCH_ASSOC);

	foreach($comments as $row) {
		$query = "SELECT * from `images` WHERE item_id = '{$row['id']}' LIMIT 1";
		$img = $dbc->query($query)->fetch(PDO::FETCH_ASSOC);

		?>
		<tr align="center" valign="top">
			<td align="center" valign="top"><?php echo $row['id'] ?></td>
			<td align="center" valign="top"><?php echo $row['user_id'] ?></td>
			<td align="center" valign="top"><?php echo $row['cat_id'] ?></td>
			<td align="center" valign="top"><?php echo $row['name'] ?></td>
			<td align="center" valign="top"><?php echo $row['price'] ?></td>
			<td align="center" valign="top"><?php echo $row['description'] ?></td>
			<td align="center" valign="top"><?php echo $row['date'] ?></td>
			<td align="center" valign="top"><?php echo $row['notified'] ?></td>
			<td align="center" valign="top"><img src="../imgs/<?php echo $img['name'] ?>" width="145"
			                                     height="145"></td>
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

<h2>Insert a new comment:</h2>
<?php
$query = "SELECT * from `users` ORDER BY id";
$users = $dbc->query($query)->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * from `categories` ORDER BY id";
$categories = $dbc->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
	User:
	<select name="user_id">
		<?php foreach($users as $user) : ?>
			<option value="<?php echo $user['id'] ?>"><?php echo $user['username'] ?></option>
		<?php endforeach; ?>
	</select><br>

	Category:
	<select name="cat_id">
		<?php foreach($categories as $category) : ?>
			<option value="<?php echo $category['id'] ?>"><?php echo $category['cat'] ?></option>
		<?php endforeach; ?>
	</select><br>
	Name:<input type="text" name="name" size="30"><br>
	Price:<input type="number" min="1" step="1" name="price"><br>
	Description:<input type="text" name="description" size="50"><br>
	Date:<input type="datetime-local" name="date"><br>
	Image:<input type="file" name="image"><br>
	<input type="submit" name="insert" value="Insert">
</form>

<?php
$dbc = null;
?>
</body>
</html>