<?php
class TwilioMessage {
	public $to, $msg, $callbackUrl, $result;
	public function __construct($to,$msg,$dataObj) {
		$this->result = 0;
		$this->to = $to;
		$this->msg = $msg;
		$this->callbackUrl = TwilioHelper::prepUrl('/wp-json/petguardian/v1/twilio-response');
		$this->dataObj = $dataObj;
	}	
	public function send() {
		if (PhoneNumber::invalid($this->to)) {
			$this->result = -1;
		} else {
			try {
			  TwilioHelper::sendMsg($this->msg,$this->to,$this->callbackUrl);
			  $this->result = 1;
			} catch (Exception $e) {
				//mark number as bad, update user meta, send emails
				$this->badNumber();
				$this->result = -1;
			}
		}
	}
	public function badNumber() {
		//PhoneNumber::updateNumberHealth($phoneNumber->number,'failed'); 
		echo "bad number!";
		if((is_a($this->dataObj, 'Guardian'))) {
			echo 'bad Guardian! number...';
			$guardian = $this->dataObj;
			$owner = new PetOwner($guardian->petId);
			UserHelper::updateGuardianNumber($owner->user->ID,$guardian->petNum,$guardian->guardNum,'___');
		}
		//mail ( 'admin@petguardianinc.com' , 'Bad Number: Pet Guardian' , $e->getMessage() );

	}
	public static function msgResults($messages) {
		$good = 0;
		$bad = 0;
		foreach ($messages as $m) {
			if ($m->result === -1) {$bad++;}
			elseif ($m->result === 1) {$good++;}
		}
		return ['good'=>$good,'bad'=>$bad];
	}
}