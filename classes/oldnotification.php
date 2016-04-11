<?php
class OldNotification {
	static public function createMessage($post,$pet,$guardian=true) {
		$name = $post['input_6_2'].' '.$post['input_6_3'].' '.$post['input_6_6'];
		$phone = $post['input_10'];
		$msg = $post['input_8'];
		if($guardian) {
			$msg .= ' View the petfile(s) at '.TwilioHelper::petfileUrl($pet);
		}
		$str = "Pet Guardian Alert! First Responder: $name, Phone: $phone. $msg";
		return $str;
	}
	static public function createAndSend($user,$pet_owner_id,$post) {
		$data = get_metadata('user', $user->ID);
		$primary = rgar(rgar($data,'mobile_phone'),0);
		$pets = array();
		$numPets = Pet::numOfPets($data);
		for($i=1;$i<($numPets+1);$i++) {
			$pets[$i] = Pet::getPet($pet_owner_id,$i,$data);
			$pets[$i]->msg = TwilioHelper::createMessage($post,$pets[$i]);
		}
		$str = TwilioHelper::createMessage($post,rgar($pets,0),false);			
		TwilioHelper::sendAlerts($str,$primary,$pets,$user->ID);
	}
	static public function sendAlerts($str,$primary,$pets,$userId) {
		$okay = 'true';
		$primary = TwilioHelper::alertPrimary($str,$primary,$userId);
		if($primary == 0) {
			$msg = "Warning: We were unable to send a message to the primary pet owner. ";
		} else {
			$msg = "Message sent to the primary pet owner. ";
		}
		$alerted = TwilioHelper::alertGuardians($pets,$userId);
		if($alerted->sent > 0 ) {
			$msg .= "We are attempting to send ".$alerted->sent." messages to Pet Guardians. ";
		}
		if($alerted->failed > 0) {
			$msg .= "Warning: We were unable to send ".$alerted->failed." messages to Pet Guardians. ";
		}
		if ($primary == 0 && $alerted->sent == 0) {$okay = 'false';}
		TwilioHelper::createConfirmation($okay,$msg);
	}
	static public function alertPrimary($str,$number,$userId) {
		if($number == '' || $number =='_____') {
			return 0;
		}
		$phoneNumber = PhoneNumber::lookup($number,$userId);
		if($phoneNumber->health != "bad") {
			try {
			   TwilioHelper::sendMsg($str,$number);
			} catch (Exception $e) {
				PhoneNumber::updateNumberHealth($number,'failed'); 
				UserHelper::updatePrimaryNumber($userId,"_____");
				mail ( 'admin@petguardianinc.com' , 'Bad Number: Pet Guardian' , $e->getMessage() );
				return 0;
			}
			return 1;
		}
	}
}