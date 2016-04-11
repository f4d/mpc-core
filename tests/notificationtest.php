<?php
class NotificationTest extends Microtest {
	protected function setup() {
		$this->log("NotificationTest setup.");
		$this->add('parsePetfile');
	}
	protected function cleanup() {
		$this->log("NotificationTest cleanup.");
	}
	public function parsePetfile() {
		$str .= ' View the petfile(s) at {{petfileUrl}}';
		echo Notification::parsePetfile($str,'www.petfile1.com');
		return 1;
	}
}