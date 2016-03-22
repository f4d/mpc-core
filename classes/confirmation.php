<?php
class Confirmation {
	const MESSAGE_FIELD = 'input_12';
	static public function createConfirmation($successful,$message) {
		//$_POST[TwilioHelper::SUCCESS_FIELD] = $successful;
		$_POST[TwilioHelper::MESSAGE_FIELD] = $message;
	}
}