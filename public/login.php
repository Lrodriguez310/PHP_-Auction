<?php
use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\User;


// (A) ADD CODE TO REQUIRE THE BOOTSTRAP FILE BELOW
require_once(__DIR__.'/../app/bootstrap.php');

if(isset($_POST['submit'])) {

    //Attempt to authenticate the user given the email and password. Returns the user object if successful or false if unsuccessful
    $user = User::auth($_POST['email'], $_POST['password']);

    if(!$user) {
        header("Location: login.php?error=failedlogin");
        die();
    }
    //If the user has been verified
    if($user->get('active')) {

        // (B) REGISTER THE USER OBJECT TO THE SESSION OBJECT USING THE METHOD IN THE SESSION CLASS BELOW
        $user = $session->login($user);

        if(!isset($_GET['ref'])) {
            header("Location: index.php");
            die();
        }
        switch($_GET['ref']) {
            case "addbid":
                header("Location: itemdetails.php?id=" . $_GET['id'] . "#bidbox");
                die();
                break;

            case "newitem":
                header("Location: newitem.php");
                die();
                break;

            case "images":
                header("Location: addimages.php?id=" . $_GET['id']);
                die();
                break;
        }

    } else {
        require(__DIR__ . "/../app/Layouts/header.php");
        try {
            echo User::displayError("notverified");
        } catch(ClassException $e) {
            Logger::getLogger()->error("Invalid error code: ", ['exception' => $e]);
            die();
        }
    }
} else {

    require(__DIR__ . "/../app/Layouts/header.php");

    echo "<h1>Login</h1>";

    if(isset($_GET['error'])) {
        try {
            // (C) RETRIEVE THE ERROR MESSAGE USING THE CORRECT METHOD IN THE USER CLASS
            $errorMsg = User::displayError("invalid username");

        } catch(ClassException $e) {
            Logger::getLogger()->error("Invalid error code: ", ['exception' => $e]);
            die();
        }
        echo $errorMsg;
    }

    ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

        <table>
            <tr>
                <td>Email Address</td>
                <td><input type="text" name="email"></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="password" name="password"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Login!"></td>
            </tr>
        </table>
    </form>
    Don't have an account? Go and <a href="register.php">Register</a>!
    <?php
}

require(__DIR__ . "/../app/Layouts/footer.php");
?>