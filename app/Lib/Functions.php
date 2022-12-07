<?php

use App\Models\Payment;
/**
 * @param $value
 * @param $function
 * @param $redirect
 * @return int
 */
function pf_validate_number($value, $function, $redirect) {
	if(isset($value) == true) {
		if(is_numeric($value) == false) {
			$error = 1;
		} else {
			$error = 0;
		}

		if($error == 1) {
			header("Location: " . $redirect);
			die();
		} else {
			$final = $value;
		}
	} else {
		if($function == 'redirect') {
			header("Location: " . $redirect);
			die();
		}

		if($function == "value") {
			$final = 0;
		}
	}

	return $final;
}
$generateButton = function ($id) {
    return Payment::generatePayment($id);
};
