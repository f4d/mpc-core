<?php
class TwilioMessageTest extends Microtest {
	protected function setup() {
		$this->log("TwilioMessageTest setup.");
		$this->add('msgValidGuardians');
	}
	protected function cleanup() {
		$this->log("NotificationTest cleanup.");
	}
	public function msgValidGuardians() {
		$this->log("parseOwnerTemplate test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$n = new Notification($owner);
		$guardians = $owner->getValidGuardians();
		$post = Notification::filterPost();
		foreach ($guardians as $guardian) {
			$template = Notification::getTemplate(7);
			$msg = $n->parseGuardianTemplate($template,$post,$guardian);
			$message = new TwilioMessage($guardian->mobile_phone,$msg);
			$message->send();
		}
		return true;
	}
}