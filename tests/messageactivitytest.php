<?php
class MessageActivityTest extends Microtest {
	protected function setup() {
		$this->log("MessageActivityTest setup.");
		$this->add('testGuardianResponse');
	}
	protected function cleanup() {
		$this->log("NotificationTest cleanup.");
	}
	public function testGuardianResponse() {
		$user = UserHelper::findUser('9123456789');
		$this->log(print_r($user,TRUE));
		$this->log("testing MessageActivity:GuardianResponse.");
		return false;
	}
}