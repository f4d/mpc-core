<?php
/*
Forms to monitor for writing data to GuardianshipMessagesSent: Form ID's: 65, 67, 6, 57, 58, 59, 60
Forms to monitor for Update Owner with Guardian Response: Form ID's: ​64
Forms to monitor for MPC Alerts (Heroku/Twilio SMS): Please update me on the logic in place and whether its using form ID 69 "Dummy" or form 68 "Phone Numbers".
*/

/**
 * MessageActivity is a utility class that helps log request and reminder messages sent to guardians.
 */
class MessageActivity {
	const GUARDIAN_UPDATE = "Guardian_Update";
	public $type, $source, $name, $to, $messageId, $timestamp;
	/**
	 * @param string $name    Name of the notification to be blocked.
	 * @param string $to      Email of the person being notified.
	 */
	public function __construct($type,$source,$name,$to,$messageId=0) {
		$this->type = $type;
		$this->source = $source;
		$this->name = $name;
		$this->to = $to;
		$this->messageId = $messageId;
		$date = new DateTime();
		$this->timestamp = $date->getTimestamp();		
	}
	public function toJson() {
		return json_encode($this);
	}
	public function writeMetaData($user,$data) {
		return $meta.$data;
		//add_user_meta( $user_id, MessageActivity::METAKEY, $awesome_level);
		//<?php update_user_meta( $user_id, $meta_key, $meta_value, $prev_value ); 
	}
	static public function createGuardianReminder($source, Guardian2 $guardian, $notificationId) {

	}
	static public function createGuardianRequest($source, Guardian2 $guardian, $notificationId) {

	}
	static public function createGuardianUpdatedNotification() {

	}
	static public function validTypes() {
		return [MessageActivity::GUARDIAN_UPDATE];
	}
}