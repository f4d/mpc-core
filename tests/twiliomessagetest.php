<?php
class TwilioMessageTest extends Microtest {
	protected function setup() {
		$this->log("TwilioMessageTest setup.");
		$this->add('msgValidGuardians');
		$this->add('msgPrimary');
	}
	protected function cleanup() {
		$this->log("TwilioMessageTest cleanup.");
		$owner = new PetOwner('cyborgk@gmail.com');
		UserHelper::updateGuardianNumber($owner->user->ID,1,3,'(555)555-5555');
	}
	public function msgValidGuardians() {
		$this->log("msgValidGuardians test.");
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
		return ($results->attempted === 2);
	}
	public function msgPrimary() {
		$this->log("msgPrimary test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$n = new Notification($owner);
		$post = Notification::filterPost();
		if ($_SERVER['SERVER_NAME']==="localhost") {$templateId = '8';}
		else {$templateId = '8'; }
		$template = Notification::getTemplate($templateId);
		$msg = $n->parseOwnerTemplate($template,$post);
		$message = new TwilioMessage($owner->phone,$msg,$owner);
		$message->send();
		if($message->result === -1) {
			$msg = "Warning: We were unable to send a message to the primary pet owner. ";
		} else {
			$msg = "Message sent to the primary pet owner. ";
		}
				return ($msg === "Message sent to the primary pet owner. ");


	}
}