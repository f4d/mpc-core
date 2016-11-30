<?php
/**
 * KillItem is a utility class that works with the Notify class,
 * to provide a list of notifications that should be blocked.
 */
class MessageActivity {
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
}