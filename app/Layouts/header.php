<?php require_once(__DIR__ . "/../bootstrap.php");?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo CONFIG_AUCTIONNAME; ?></title>
    <link rel="stylesheet"  href="<?php echo CONFIG_URL ?>/css/stylesheet.css" type="text/css">
</head>
<body>
    <div id="header">
        <h1><?php echo CONFIG_AUCTIONNAME;?></h1>
    </div>
    <div id="menu">
        <a href="../public/index.php">Home</a> &bull;
        <?php
        if($session->isLoggedIn()){
            echo "<a href='logout.php'>Logout</a>&bull;";
        }else{
            echo "<a href='login.php'>Login</a> &bull;";
        }
        ?>
        <a href="newitem.php">New Item</a> &bull;
        <a href="processauctions.php">ProcessAuction</a>
    </div>
    <div id="container">
        <div id="bar">
            <?php require_once("bar.php");?>
        </div>
        <div id="main">





