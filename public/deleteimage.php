<?php

use App\Exceptions\ClassException;
use App\Exceptions\FileException;
use App\Lib\File;
use App\Lib\Logger;
use App\Models\Image;
use App\Models\Item;

require_once(__DIR__ . "/../app/bootstrap.php");

if(isset($_GET['image_id']) || isset($_GET['item_id'])) {
    $validimageid = pf_validate_number($_GET['image_id'], "redirect", CONFIG_URL);
    $validitemid = pf_validate_number($_GET['item_id'], "redirect", CONFIG_URL);
} else {
    die("Invalid ID");
}

if(isset($_POST['submityes'])) {

    //Retrieve the item and image from the database and instantiate a new instance of the item and image class
    try {
        $itemObj = Item::findFirst(["id" => "$validitemid"]);
    } catch(ClassException $e) {
        Logger::getLogger()->critical("Invalid Item: ", ['exception' => $e]);
        echo "Invalid Item";
        die();
    }

    try {
        $imageObj = Image::findFirst(["id" => "$validimageid"]);
    } catch(ClassException $e) {
        Logger::getLogger()->critical("Invalid Image: ", ['exception' => $e]);
        echo "Invalid Image";
        die();
    }

    try {
        // (A) Use the file class "deleteFile" method to delete the image from the image store
        $image = File::deleteFile(FILE_UPLOADLOC);

    } catch(FileException $e) {
        Logger::getLogger()->error("could not delete file: ", ['exception' => $e]);
    }
    try {
        $imgObj = Image::findFirst(["name" => $imageObj->get("name")]);
    } catch(ClassException $e) {
        Logger::getLogger()->critical("Invalid Image: ", ['exception' => $e]);
        echo "Invalid Image";
        die();
    }

    // (B) Use the delete method to remove the record from the database
    $image = $imgObj->delete();
    header("Location: addimages.php?id=" . $itemObj->get("id"));
    die();

}

if(isset($_POST['submitno'])) {
    header("Location: addimages.php?id=" . $validitemid);
    die();
}

require(__DIR__ . "/../app/Layouts/header.php");
?>

    <h2>Delete image?</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        Are you sure you want to delete this image?
        <p>
            <input type="submit" name="submityes" value="Yes"> <input type="submit" name="submitno" value="No">
        </p>
    </form>

<?php
require(__DIR__ . "/../app/Layouts/footer.php");
?>