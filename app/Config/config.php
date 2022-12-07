<?php

//Database Configuration


define("DB_HOST", "localhost");
define("DB_USER", "lrodriguez");
define("DB_PASSWORD", "jhk8tjhk8tj2rprj2rpr");
define("DB_NAME", "lrodriguezauction");

// add your name below
define("CONFIG_ADMIN", "Luis Rodriguez");
define("CONFIG_ADMINEMAIL", "w0812903@stclairconnect.ca");
// location of your forums below
define("CONFIG_URL", "https://lrodriguez.scweb.ca/Auction/public");
// add your blog name below
define("CONFIG_AUCTIONNAME", "Pokemon Auction");
// cureency used on the auction
define("CONFIG_CURRENCY", "$");

define("CLIENT_ID", "AWrAfrOTEzELr8atnt1Oh6TUHWD9upS9I4biRlqD5OiNYEK-5-S8MF-KVJSxrmiFbcywWZAETHP4Qaqg");
define("CLIENT_SECRET", "EFsEQ34jtc2zkF5EbJK4B7LoqiJ_2VRKxeXVfHZziOAttSd75vwvOnR7X-Wvg8FOn4zLr-ERlGB9Z_qK");
// DEFAULT CURRENCY CA
define("PAYPAL_CURRENCY", "CAD");
// USER REDIRECTION ADDRESS AFTER SUCCESS PAYMENT
define("PAYPAL_RETURNURL", CONFIG_URL . "/payment-successful.php");
//user redirection address after cancelled payment
define("PAYPAL_CANCELURL", CONFIG_URL . '/payment-cancelled.php');

//set timezone
date_default_timezone_set("America/Toronto");

//log location
define("LOG_LOCATION", __DIR__ . "/../../logs/app.log");

//File Upload Location
define("FILE_UPLOADLOC", "imgs/");