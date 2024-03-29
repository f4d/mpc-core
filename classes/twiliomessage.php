<?php
class TwilioMessage {
	public $to, $msg, $callbackUrl, $result;
	public function __construct($to,$msg,$dataObj) {
		$this->result = 0;
		$this->to = PhoneNumber::cleanup($to);
		$this->msg = html_entity_decode($msg, ENT_QUOTES | ENT_XML1, 'UTF-8');
		$this->msg = str_replace("\\", "", $this->msg);
		mail('cyborgk@gmail.com', 'twilio message', $this->msg);
		$this->callbackUrl = TwilioHelper::prepUrl('/wp-json/petguardian/v1/twilio-response');
		$this->dataObj = $dataObj;
	}	
	public function send() {
		$phoneNumber = PhoneNumber::lookup($this->to);
		mail('cyborgk@gmail.com', 'twilio message 2', $this->msg);
		//$encoding = mb_detect_encoding($this->msg);
		//mail('cyborgk@gmail.com', 'twilio encoding', print_r($encoding,TRUE));

		//SKIP BAD NUMBERS
		if (PhoneNumber::invalid($this->to) || $phoneNumber->health == "bad") {
			$this->result = -1;
		} else {
			try {
			  TwilioHelper::sendMsg($this->msg,$this->to,$this->callbackUrl);
			  $this->result = 1;
			} catch (Exception $e) {
				//mark number as bad, update user meta, send emails
				$error = $e->getMessage();
				if(strpos($error,'not a valid phone number')>0) {$this->badNumber();}
				$this->result = -1;
			}
		}
	}
	public function badNumber() {
		//PhoneNumber::updateNumberHealth($phoneNumber->number,'failed'); 
		//echo "bad number!";
		if((is_a($this->dataObj, 'Guardian'))) {
			//echo 'bad Guardian! number...';
			$guardian = $this->dataObj;
			$owner = new PetOwner($guardian->petId);
			UserHelper::updateGuardianNumber($owner->user->ID,$guardian->petNum,$guardian->guardNum,'___');
		}
		//mail ( 'admin@petguardianinc.com' , 'Bad Number: Pet Guardian' , $e->getMessage() );

	}
	public static function msgResults($messages) {
		$attempted = 0;
		$failed = 0;
		foreach ($messages as $m) {
			if ($m->result === -1) {$failed++;}
			elseif ($m->result === 1) {$attempted++;}
		}
		return (object) ['attempted'=>$attempted,'failed'=>$failed];
	}
	static public function alertGuardians($owner,$templateId,$from='') {
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$guardians = $owner->getValidGuardians();
		$post = Notification::filterPost();
		$sent = [];
		foreach ($guardians as $guardian) {
			$template = Notification::getTemplate($templateId);
			$msg = $n->parseGuardianTemplate($template,$post,$guardian,$from);
			$message = new TwilioMessage($guardian->mobile_phone,$msg,$guardian);
			//print_r($message);
			$message->send();
			$sent[] = $message;
		}
		$results = TwilioMessage::msgResults($sent);
		$msg = "We are attempting to send ".$results->attempted." messages to Pet Guardians. ";
		if ($results->failed > 0) {
			$msg .= "Warning: We were unable to send ".$results->failed." messages to Pet Guardians. ";
		}
		return $msg;
	}
	static public function alertPrimary($owner,$templateId,$from='') {
		$n = new Notification($owner);
		$post = Notification::filterPost();

		$template = Notification::getTemplate($templateId);
		$msg = $n->parseOwnerTemplate($template,$post,$from);
		$message = new TwilioMessage($owner->phone,$msg,$owner);
		$message->send();
		if($message->result === -1) {
			$msg = "Warning: We were unable to send a message to the primary pet owner. ";
		} else {
			$msg = "Message sent to the primary pet owner. ";
		}
		return $msg;
	}	
	static public function alertGuardiansForPet($owner,$petNum,$templateId,$from='') {
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$guardians = $owner->getValidPetGuardians($petNum);
		$post = Notification::filterPost();
		$sent = [];
		foreach ($guardians as $guardian) {
			$template = Notification::getTemplate($templateId);
			$msg = $n->parseGuardianTemplate($template,$post,$guardian,$from);
			$message = new TwilioMessage($guardian->mobile_phone,$msg,$guardian);
			$message->send();
			$sent[] = $message;
		}
		$results = TwilioMessage::msgResults($sent);
		$msg = "We are attempting to send ".$results->attempted." messages to Pet Guardians. ";
		if ($results->failed > 0) {
			$msg .= "Warning: We were unable to send ".$results->failed." messages to Pet Guardians. ";
		}
		return $msg;
	}
}