<?php
class TwilioMessage {
	public function __construct($to,$msg,$callbackUrl) {
		$this->to = $to;
		$this->msg = $msg;
		$this->callbackUrl = $callbackUrl;
	}	
	public function send() {
		TwilioHelper::sendMsg($this->msg,$this->to,$this->callbackUrl);
	}
}