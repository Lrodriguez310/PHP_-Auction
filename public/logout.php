<?php
// (A) ADD CODE TO REQUIRE THE BOOTSTRAP FILE BELOW
require_once(__DIR__.'/../app/bootstrap.php');

//Run the session logout method to logout the current user
// (B) USE THE CORRECT SESSION CLASS METHOD TO LOGOUT THE USER
$user = $session->logout();

// (C) REDIRECT THE USER TO THE index.php PAGE
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

die();