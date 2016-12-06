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
	const METAKEY = "GuardianNotifications";
	public $type, $source, $to, $messageId, $timestamp;
	/**
	 * @param string $name    Name of the notification to be blocked.
	 * @param string $to      Email of the person being notified.
	 */
	public function __construct($type,$source,$to,$messageId=0) {
		$this->type = $type;
		$this->source = $source;
		$this->to = $to;
		$this->messageId = $messageId;
		$date = new DateTime();
		$this->timestamp = $date->getTimestamp();		
	}
	public function toJson() {
		return json_encode($this);
	}
	public function addNotification($userId) {
		$metaStr = get_user_meta($userId, MessageActivity::METAKEY, TRUE);
		$notifications = json_decode($metaStr);
		$notifications[] = $this;
		$metaStr = json_encode($notifications);
		update_user_meta($userId, MessageActivity::METAKEY, $metaStr);
		return $metaStr; 
	}
	
	static public function createGuardianEmail($notification,$guardian) {
		return new MessageActivity($notification['name'],'email',$guardian->to,$notification['id']);
	}

}