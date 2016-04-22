<?php
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
	public static function invalid($number) {
		if ( $number == '' || $number == '_____' ) {
			return true;
		} else {
			return false;
		}
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
		//print_r($entries);
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
			//print_r($p);
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
			print_r($number);
			$status = $_POST['SmsStatus'];
			return PhoneNumber::updateNumberHealth($number,$status);
		} else {
			return "Invalid query";
		}
	}
	static public function markInvalid($userId,$data) {

	}
}
