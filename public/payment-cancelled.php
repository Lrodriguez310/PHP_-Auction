<?php
require_once(__DIR__ . "/../app/bootstrap.php");

require(__DIR__ . "/../app/Layouts/header.php");

echo <<<CANCELLED_
	<h1>Payment Cancelled</h1>
	<p>Your payment was cancelled.</p>
CANCELLED_;
require(__DIR__ . "/../app/Layouts/footer.php");
