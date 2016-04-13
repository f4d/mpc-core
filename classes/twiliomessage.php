<?php
class TwilioMessage {
	public function __construct($to,$msg) {
		$this->to = $to;
		$this->msg = $msg;
		$this->callbackUrl = TwilioHelper::prepUrl('/wp-json/petguardian/v1/twilio-response');;
	}	
	public function send() {
		TwilioHelper::sendMsg($this->msg,$this->to,$this->callbackUrl);
	}
}