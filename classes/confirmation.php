<?php
class Confirmation {
	const MESSAGE_FIELD = 'input_12';
	static public function createConfirmation($message) {
		$_POST[Confirmation::MESSAGE_FIELD] = $message;
	}
	static public function createGenericConfirmation($message,$fieldId) {
		$field = "input_$fieldId";
		$_POST[$field] = $message;
	}	
}