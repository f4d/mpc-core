<?php
class SubsequentSumbissions {

	public function __construct() {

	}

	public function add_action($action,$method, $args=1) {
		add_action( $action, [$this, $method], 100, $args );
	}
	/**
	 * filter_add_pets runs when the Add Pets & Guardians form is submitted
	 * @param  array $form   gravity forms form data
	 * @return n/a
	 */
	public function filter_add_pets($form) {
		$user = wp_get_current_user();
		$meta = get_metadata('user', $user->ID);
		$data = Pet2::post2Data($_POST);
		//print_r($data);
		$pet_owner_id = SubmissionsHelper::petOwnerId($meta);	
		//get the number of pets from form, NOT meta!
		$numPets = (int) rgar($_POST,'input_59');
		$pets = array();
		$killArr = [];
		for($i=1;$i<($numPets+1);$i++) {
			if(SubmissionsHelper::isPetNew($i,$meta)) {
				//echo "We got one!";
				//if new pet, add guardians reminders to kill list
				$killArr = array_merge($killArr,KillItem::killReminders($i));
			} else {
				$pets[$i] = new Pet( $i, $pet_owner_id, $meta );
			}
		}
		//now we take care of pets that aren't new
		foreach($pets as $pet) {
			$newPet = Pet2::getPet($pet->petfile,$data);
			//print_r($newPet);
			$killArr = Pet2::checkGuardianNotifications($pet,$newPet,$killArr);
		}
		$json = Notify::createJson($killArr);
		$_POST['input_239'] = $json;
		GFCommon::log_debug( __METHOD__ . '(): logging Add Pet json-kill-list: '.print_r($json, true) );
	}
/**
 * add_pet_notification sets the notification blocking filter to run on the 
 * Add Pets & Guardians form, and provides the hidden field ID 239
 */
	public function add_pet_notification($notification, $form, $entry) {
		$this->filter_notifications('239',$notification,$entry);
		return $notification;
	}
/**
 * filter_petfile1 checks data submitted with petfile1, and writes json data
 * to form field 205 to be decoded in notification phase
 */
	public function filter_petfile1($form) {
		$this->filter_petfile(1,205);
	}
	public function filter_petfile2($form) {
		$this->filter_petfile(2,205);
	}
	public function filter_petfile3($form) {
		$this->filter_petfile(3,205);
	}
	public function filter_petfile4($form) {
		$this->filter_petfile(4,205);
	}
	public function filter_petfile5($form) {
		$this->filter_petfile(5,205);
	}			
/**
 * petfile1_notification sets the notification blocking filter to run on the 
 * Petfile1 form, and provides the hidden field ID 205
 */
	public function petfile1_notification($notification, $form, $entry) {
		$this->filter_notifications('205',$notification,$entry);
		return $notification;
	}
	/**
	 * filter_petfile was designed to be run when petfile forms are updated,
	 * to block guardian reminders and requests as needed.
	 * @param  [integer 1-5] $petNum
	 * @param  [type]
	 * @return [type]
	 */
	public function filter_petfile($petNum) {
		//get the current user and their metadata
		$user = wp_get_current_user();
		$meta = get_metadata('user', $user->ID);

		$pet_owner_id = SubmissionsHelper::petOwnerId($meta);	
		//Change the submitted form data to a format that matches user meta data
		//change the following line if rewriting to grab data from other forms...
		$data = Pet2::petfilePost2Data($petNum,$_POST);

		$requestArr = [];
		$reminderArr = [];
		$totalG = SubmissionsHelper::numberGuardians($petNum,$meta);
		//otherwise, we need to see what requests & reminders were sent, 
		//and add all the reminders we are currently sending
		for($g=1;$g<$totalG+1;$g++) {
			//have the responded?

			//are they new?

		}
	}
	/**
	 * filter_notifications can be run on any form where you wrote 
	 * json kill list data to a hidden field, to block unwanted notifications
	 * @param  string $field        Gravity entry ID for hidden json field
	 * @param  array $notification  Gravity forms, notification data
	 * @param  array $entry         Gravity forms, form entry data
	 * @return [type]
	 */
	public function filter_notifications($field,$notification,$entry) {
		$n = new Notify($notification);
		$k = json_decode($entry[$field]);
		$n->kill_list = $k->blocked_notifications;
		//print_r($n);
		//$elog = print_r($n->kill_list, true);
		//GFCommon::log_debug( __METHOD__ . '(): UPDATE PET & GUARDIAN NOTIFICATION '.$elog );
		$n->block();

	}

	public function test_submission($f) {
		$test = new TestNewPet();
		echo $test->log;
		

		//$test = new TestSubmissions();
		//echo $test->log;

	}

	public function test_notification($notification, $form, $entry) {
		//$test = new TestNotifications(array($notification,$form,$entry));
		//echo $test->log;
		//return $notification;
	}
	public function test_notification2($notification, $form, $entry) {
		$elog = print_r($notification, true);
		GFCommon::log_debug( __METHOD__ . '(): UPDATE PET & GUARDIAN NOTIFICATION '.$elog );
		return $notification;
	}
	public function add_notification_filter( $form ) {
		add_filter( 'gform_notification', array( $this, 'evaluate_notification_conditional_logic' ), 10, 3 );
		return $form;
	}
	public function add_manual_notification_event( $events ) {
		$entries = GFAPI::get_entries( '62' );
		$events['manual'] = __( 'Subsequent Submission' );
		return $events;
	}

}