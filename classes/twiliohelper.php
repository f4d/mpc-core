<?
class TwilioHelper {
	static public function sendToGroup($str,$toArr,$callbackUrl) {
		foreach ($toArr as $to) {
			TwilioHelper::sendMsg($str,$to,$callbackUrl);
		}
	}
	static public function sendMsg($str,$to,$callbackUrl) {
		$account_sid = "ACb7c5f3d51adb05223c640ffaff969b46"; // Your Twilio account sid
		$auth_token = "d54280461d5603d9cc2217ca2b79ab62"; // Your Twilio auth token
		$client = new Services_Twilio($account_sid, $auth_token);
		//$callbackUrl = TwilioHelper::prepUrl('/wp-json/petguardian/v1/twilio-response');
		$message = $client->account->messages->create(array( 
			'To' => TwilioHelper::scrubPhone($to), 
			'From' => " +13134448630", 
			'Body' => $str, 
			'StatusCallback' => $callbackUrl
		));
		$sid = $message->sid;
	}
	static public function petfileUrl($pet) {
		return $pet->findPetfileUrl().' ';
	}
	public static function scrubPhone($number) {
		return '+1'.$number;
	}	
	static public function prepUrl($url) {
		$http = "http://";
		if (array_key_exists('HTTPS', $_SERVER)) {
			$http = "https://";
		} 

		return $http.TwilioHelper::getServerName().$url;
	}
	static public function getServerName() {
		if ($_SERVER['SERVER_NAME']==="localhost") {
			return "petguardian.staging.wpengine.com";
		} else {
			return $_SERVER['SERVER_NAME'];
		}
	}
}
