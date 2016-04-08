<?php
class NotificationTest extends Wp_Microtest {
	protected function setup() {
		$this->log("NotificationTest setup.");
		$this->add('dummy_pass');
		$this->add('dummy_fail');
	}
	protected function cleanup() {
		$this->log("NotificationTest cleanup.");
	}
	public function parsePet() {
		
	}
}