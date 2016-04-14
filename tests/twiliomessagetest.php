<?php
class TwilioMessageTest extends Microtest {
	protected function setup() {
		$this->log("TwilioMessageTest setup.");
		$this->add('msgValidGuardians');
	}
	protected function cleanup() {
		$this->log("TwilioMessageTest cleanup.");
		$owner = new PetOwner('cyborgk@gmail.com');
		UserHelper::updateGuardianNumber($owner->user->ID,1,3,'(555)555-5555');
	}
	public function msgValidGuardians() {
		$this->log("parseOwnerTemplate test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$n = new Notification($owner);
		$guardians = $owner->getValidGuardians();
		print_r($guardians);
		$post = Notification::filterPost();
		$sent = [];
		foreach ($guardians as $guardian) {
			$template = Notification::getTemplate(7);
			$msg = $n->parseGuardianTemplate($template,$post,$guardian);
			$message = new TwilioMessage($guardian->mobile_phone,$msg,$guardian);
			$message->send();
			$sent[] = $message;
		}
		$results = TwilioMessage::msgResults($sent);
		echo "<br><br>";
		$owner = new PetOwner('cyborgk@gmail.com');
		$guardians = $owner->getValidGuardians();

		print_r($guardians);
		echo "<br>";
		print_r($results);
		echo "<br>";
		return ($results['good'] === 2);
	}
}