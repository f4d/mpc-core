<?php
class Pet {
	const PET_OWNER_FIELD = '204';
	const PF1_ID = '6';
	const PF2_ID = '57';
	const PF3_ID = '58';
	const PF4_ID = '59';
	const PF5_ID = '60';
	public $petOwnerId, $petfile, $msg, $name;
	public function __construct( $petfile, $petOwnerId, $data ) {
		$this->petOwnerId = $petOwnerId;
		$this->petfile = $petfile;
		$this->name = rgar(rgar($data,"pet_{$petfile}_name"),0);
		$this->guardians = array();
		$this->data = $data;
	}
	public function setGuardian($guardianNum,$data) {
		$this->guardians[$guardianNum] = new Guardian($data);
	}
	public function findPetfileUrl() {
		$petfileArr = array('1'=>Pet::PF1_ID,'2'=>Pet::PF2_ID,'3'=>Pet::PF3_ID,
			'4'=>Pet::PF4_ID,'5'=>Pet::PF5_ID);
		//use $this->petfile & $this->petOwnerId, lookup in petfile{n} gravityform
		$search_criteria = array();
		$search_criteria['field_filters'][] = array( 
			'key' => Pet::PET_OWNER_FIELD, 
			'value' => $this->petOwnerId );
		$entries = GFAPI::get_entries( $petfileArr[$this->petfile], $search_criteria );
		$last = array_shift($entries);
		$callbackUrl = TwilioHelper::prepUrl('/guardian-access-petfile-1/?eid='.$last['id']); 
		return $callbackUrl;
	}
	static public function numOfPets($data) {
		$pets = rgar($data,'how_many_pets_owned');
		if(rgar($pets,0) != '') {
			return (int) $pets[0];
		} else {
			return 0;
		}		
	}
	static public function getPet($petOwnerId,$petNum,$data) {
		$pet = new Pet($petNum,$petOwnerId,$data);
		//set info for each of the pet guardians
		for($i=1;$i<6;$i++) {
			$prefix = "p{$petNum}_guardian_{$i}_";
			$arr = array('prefix','first_name','last_name','email','mobile_phone','response');
			$hash = array();
			foreach($arr as $a) {
				$tempArr = rgar($data,$prefix.$a);
				$hash[$a] = rgar($tempArr,0);
			}
			$pet->setGuardian($i,$hash);
		}
		return $pet;
	}
}
class Guardian {
	public $prefix, $first_name, $last_name, $email, $mobile_phone, $response;
	public function __construct( $meta ) {
		$this->mobile_phone = rgar( $meta, 'mobile_phone' );
		$this->response = rgar( $meta, 'response' );
		$this->prefix = rgar( $meta, 'prefix' );
		$this->first_name = rgar( $meta, 'first_name' );
		$this->last_name = rgar( $meta, 'last_name' );
		$this->email = rgar( $meta, 'email' );
	}
}
class PhoneNumber {
	const FORM_ID = '68';
	const PHONE_FIELD = '4';
	const HEALTH_FIELD = '2';
	const USER_ID_FIELD = '3';

	public $number;
	public $health; 
	public $userId;
	public function __construct($number,$health="unknown",$userId=0) {
		$this->number = PhoneNumber::cleanup($number);
		$this->health = $health;
		$this->userId = $userId;
	}
	public function setHealth($callStatus) {
		if ($callStatus=="sent" || $callStatus=="delivered") {
			$this->health = "good";
		} else if($callStatus=="failed" || $callStatus=="undelivered") {
			$this->health = "bad";
		}
	}
	public static function validNumber($number) {
		$test = (string) preg_replace("/[^0-9]/", "", $number);
		if ( $test == '5555555555' || $test == '0000000000' || strlen($test) != 10 ) {
			return false;
		}
		return true;
	}	
	public static function stripPrefix($number) {
		$arr = str_split($number);
		array_shift ( $arr );
		array_shift ( $arr );
		return implode($arr);
	}
	public static function cleanup($number) {
		//strip all non-numeric characters out
		return preg_replace("/[^0-9]/", "", $number);
	}

	public static function lookup($number, $userId = 0) {
		$number = PhoneNumber::cleanup($number);		
		$records = PhoneNumber::gfFindNumber($number);
		if (count($records)>0) {
			$entry = array_pop($records);
			$pn = new PhoneNumber($number,$entry[PhoneNumber::HEALTH_FIELD]);
		} else {

			$pn = new PhoneNumber($number,'unknown',$userId);
			if (!PhoneNumber::validNumber($number)) {
				$pn->health = "bad";
			}
			$pn->save(); 
		}
		return $pn;	
	}
	public function save() {
		$entry = array();
		$entry['form_id'] = PhoneNumber::FORM_ID;
		$entry[PhoneNumber::PHONE_FIELD] = $this->number;
		$entry[PhoneNumber::HEALTH_FIELD] = $this->health;
		$entry[PhoneNumber::USER_ID_FIELD] = $this->userId;
		GFAPI::add_entry( $entry );
	}
	public function update() {
		$entries = PhoneNumber::gfFindNumber($this->number);
		foreach ($entries as $entry) {
			$entry[PhoneNumber::PHONE_FIELD] = $this->number;
			$entry[PhoneNumber::HEALTH_FIELD] = $this->health;
			GFAPI::update_entry( $entry );
		}
	}
	public static function gfFind($number,$userId=0) {
		$search_criteria = array();
		$search_criteria['field_filters'][] = array( 'key' => PhoneNumber::PHONE_FIELD, 'value' => $number );
		if($userId != 0) {
			$search_criteria['field_filters'][] = array( 'key' => PhoneNumber::USER_ID_FIELD, 'value' => $userId );
		}
		$entries = GFAPI::get_entries( PhoneNumber::FORM_ID, $search_criteria );
		return $entries;
	}
	public static function gfFindNumber($number,$last=false) {
		$search_criteria = array();
		$search_criteria['field_filters'][] = array( 'key' => PhoneNumber::PHONE_FIELD, 'value' => $number );
		$entries = GFAPI::get_entries( PhoneNumber::FORM_ID, $search_criteria );
		if( count($entries) > 0 ) {
			if($last) {
				return $entries[0];
			} else {
				return $entries;
			}
		} else {
			return array();
		}
	}
	public static function updateNumberHealth($number,$callStatus) {
		if ($callStatus == 'sent' || $callStatus == 'failed' || $callStatus == 'undelivered' || $callStatus == 'delivered' && $number != "_____") {
			$p = PhoneNumber::gfFindNumber($number,true);
			$phoneNumber = new PhoneNumber($number,$p[PhoneNumber::HEALTH_FIELD],$p[PhoneNumber::USER_ID_FIELD]);
			$phoneNumber->setHealth($callStatus);

			$phoneNumber->update();
			if($phoneNumber->health=="bad") {
				//fix bad numbers in meta data
				$meta = get_metadata('user', $phoneNumber->userId);		
				if($meta!==false) {
					UserHelper::updateNumbers($phoneNumber->userId,$meta,$number,'5555555555');
				}
			}
			return 'Updated';
		} else {
			return "No change";
		}
	}
	static public function smsCallback( WP_REST_Request $request ) {
		if (array_key_exists('To', $_POST) && array_key_exists('SmsStatus', $_POST) ) {
			//TwilioHelper::sendMsg($_POST['SmsStatus'],'7736092730');
			$number = PhoneNumber::stripPrefix($_POST['To']);
			$status = $_POST['SmsStatus'];
			return PhoneNumber::updateNumberHealth($number,$status);
		} else {
			return "Invalid query";
		}
	}
	static public function markInvalid($userId,$data) {

	}
}
class Dummy {
	public function __construct($msg) {
		echo $msg;
	}
}