<?php
class TwilioMessageTest extends Microtest {
	public $core;
	protected function setup() {
		$this->log("TwilioMessageTest setup.");
		$this->add('msgValidGuardians');
		$this->add('msgPrimary');
		$this->add('firstResponder');
	}
	protected function cleanup() {
		$this->log("TwilioMessageTest cleanup.");
		$owner = new PetOwner('cyborgk@gmail.com');		
		//UserHelper::markGuardianAccepted($owner->user->ID,2,1);
		//UserHelper::updateGuardianNumber($owner->user->ID,2,1,'(555)555-5555');
		//UserHelper::updateGuardianNumber($owner->user->ID,3,1,'(773)609-2730');
	}
	public function updateGuardianAccepted($userId,$petNum,$guardianNum) {
		$key = UserHelper::guardianResponseKey($petNum,$guardianNum);
		update_user_meta( $userId, $key, 1 );
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
			//echo "Guardian #:";
			//echo $guardian->mobile_phone."<br>";
			//echo $guardian->response."<br>";
			//echo "Template ID: $templateId<br>";
			$template = Notification::getTemplate(Mpc_Core::getFirstResponderGuardianTemplate());
			$msg = $n->parseGuardianTemplate($template,$post,$guardian);
			$message = new TwilioMessage($guardian->mobile_phone,$msg,$guardian);
			$message->send();
			$sent[] = $message;
		}
		$results = TwilioMessage::msgResults($sent);
		echo "<br><br>";
		$owner = new PetOwner('cyborgk@gmail.com');
		$guardians = $owner->getValidGuardians();
		echo "<br>";
		foreach($guardians as $guardian) {
			echo "Guardian #:";
			echo $guardian->mobile_phone."<br>";
		}
		//print_r($results);
		echo "<br>";
		return ($results->attempted === 2);
	}
	public function msgPrimary() {
		$this->log("msgPrimary test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$template = Notification::getTemplate(Mpc_Core::getFirstResponderOwnerTemplate());
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
	public function firstResponder() {
		$this->log("firstResponder test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$confirmation = TwilioMessage::alertPrimary($owner,Mpc_Core::getFirstResponderOwnerTemplate());
		$confirmation .= TwilioMessage::alertGuardians($owner,Mpc_Core::getFirstResponderGuardianTemplate());
		$this->log('Confirmation:'.$confirmation);
		return false;
	}
}