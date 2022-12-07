<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
</head>
<body>
<?php
// Set the variables for the database access:
require_once(__DIR__ . "/../../app/bootstrap.php");

$dbc = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

if(isset($_POST['insert'])) {
	$category = trim($_POST['category']);

	$query = "INSERT into `categories` values ('0', '$category')";
	$dbc->query($query);

} elseif(isset($_POST['delete'])) {
	$id = trim($_POST['id']);

	$query = "DELETE FROM `categories` WHERE id = '$id'";
	$dbc->query($query);

}

?>

<h2 style="text-align: center">Manage Categories</h2>
<table border="1" width="50%" cellspacing="2" cellpadding="2" align="center">
    <tr>
        <th>ID (auto_increment)</th>
        <th>Cat</th>
        <th>Delete?</th>
    </tr>

	<?php
	$query = "SELECT * from `categories` ORDER BY id";
	$categories = $dbc->query($query)->fetchAll(PDO::FETCH_ASSOC);

	foreach($categories as $row) {
		?>
        <tr align="center" valign="top">
            <td align="center" valign="top"><?php echo $row['id'] ?></td>
            <td align="center" valign="top"><?php echo $row['cat'] ?></td>
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

<h2>Insert a new category:</h2>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
     Category Name: <input type="text" name="category" size="20"><br>
    <input type="submit" name="insert" value="Insert">
</form>

<?php
$dbc = null;
?>
</body>
</html>