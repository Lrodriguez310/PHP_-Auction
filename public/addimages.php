<?php

use App\Exceptions\ClassException;
use App\Exceptions\FileException;
use App\Lib\File;
use App\Lib\Logger;
use App\Models\Image;
use App\Models\Item;

require_once(__DIR__ . "/../app/bootstrap.php");

if(isset($_GET['id'])) {
    $validid = pf_validate_number($_GET['id'], "value", CONFIG_URL);

    //Find the first item by id; return the object
    try {
        $item = Item::findFirst(["id" => $validid]);
    } catch(ClassException $e) {
        Logger::getLogger()->critical("Invalid Item: ", ['exception' => $e]);
        echo "Invalid Item";
        die();
    }

    //Check if the object property 'user_id' matches the currently logged in user's id
    if($item->get('user_id')!=$session->getUser()->get('id')) {
        header("Location: index.php");
        die();
    }

}

// (A) If the user is not logged in, redirect to login page
if(!$session->isLoggedIn()) {
    header("Location: login.php?ref=images&id=$validid");
}




if(isset($_POST['submit'])) {

    //Create a file object containing the information about the uploaded file
    $file = new File('userfile');

    //If the file doesn't have a name, redirect to an error page
    if(!$file->get("name")) {
        header("Location: addimages.php?error=nophoto");
        die();

        //If the file was empty, redirect to an error page
    } elseif(!$file->get("size")) {
        header("Location: addimages.php?error=photoprob");
        die();

        //If the file exceeded the maximum file size (defined in the file class), redirect to an error page
    } elseif($file->get("size") > File::MAXFILESIZE) {
        header("Location: addimages.php?error=large");
        die();

        //If the image file does not meet the sufficient size requirements, redirect to an error page
    } elseif(!$file->get('size')) {
        header("Location: addimages.php?error=invalid");
        die();

        //else; valid file
    } else {
        //Move the file to a new directory (specified in the file class) and rename the file.
        try {
            $file->moveUploadedFile();

            // (B) Create a record for the new image file in the database
            $image = New Image($item->get('id'), $file->get("name"));
            $image->create();



            //Redirect the user
            header("Location: addimages.php?id=" . $item->get("id"));
            die();
        } catch(FileException $e) {
            Logger::getLogger()->error("could not upload file: ", ['exception' => $e]);
            echo 'There was a problem uploading your file.<br />';
        }
    }

} else {
    require(__DIR__ . "/../app/Layouts/header.php");

    echo "<h1>Current images</h1>";

    if(isset($_GET['error'])) {
        try {
            $errorMsg = Image::displayError($_GET['error']);
        } catch(ClassException $e) {
            Logger::getLogger()->error("Invalid error code: ", ['exception' => $e]);
            die();
        }
        echo $errorMsg;
    } else {
        //Load image objects into itemObj
        $item->getImages();

        //Check if there are image objects attached to the given item
        if(empty($item->get('imageObjs'))) {
            echo "No images.";
        } else {
            echo "<table>";
            //Iterate over each image object and display it to the user
            /* @var $img \App\Models\Image */
            foreach($item->get('imageObjs') as $img) {
                echo "<tr>";
                echo "<td><img src='imgs/" . $img->get('name') . "' width='100'></td>";
                echo "<td>[<a href='deleteimage.php?image_id=" . $img->get('id') . "&item_id=" . $item->get("id") . "'>delete</a>]</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>

        <form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>"
              method="POST">
            <!-- (C) HTML code to create 3 input fields -->
            <!-- Input 1 -->
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <table>
                <tr>
                    <td>Image to upload</td>
                    <td>
                        <!-- Input 2 -->
                        <input type="file" name="userfile" accept="image/*">
                    </td>
                </tr>
                <tr>
                    <td>
                        <!-- Input 3 -->
                        <input type="submit" name="submit" value="Upload File">
                    </td>
                </tr>
            </table>
        </form>

        When you have finished adding photos, go and <a
            href="<?php echo "itemdetails.php?id=" . $item->get('id'); ?>">see your item</a>!
        <?php
    }
}

require(__DIR__ . "/../app/Layouts/footer.php");