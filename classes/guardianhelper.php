<?php
/**
 * GuardianHelper is a utility class that uses only the Guardian email data.
 * (Use original Guardian class for full representation of Guardian data.)
 */
class GuardianHelper {
	public $petNum, $gNum, $to;
	public function __construct($petNum,$gNum,$meta) {
		$this->petNum = $petNum;
		$this->gNum = $gNum;
		//$arr = array('prefix','first_name','last_name','email','mobile_phone','response');
		$prefix = "p{$petNum}_guardian_{$gNum}_";
		$this->to = rgar($meta,$prefix.'email');
	}
	
	static public function findNotificationGuardian($notificationName,$meta) {
		$arr = explode(" ", $notificationName);
		$petNum = $arr[3];
		$gNum = $arr[5];
		return new GuardianHelper($petNum,$gNum,$meta);
	}
}