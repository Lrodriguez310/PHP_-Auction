<?php

require_once(__DIR__.'/Config/config.php');
require_once (__DIR__.'/Lib/Functions.php');

require_once(__DIR__. "/../vendor/autoload.php");


$session = new \App\Lib\Session();

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

$environment = new SandboxEnvironment(CLIENT_ID,
CLIENT_SECRET);
$client = new PayPalHttpClient($environment);
