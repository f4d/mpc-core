<?php
/*
	Plugin Name: MPC Core
	Plugin URI: http://musicahermetica.com
	Description: Core plugin for Million Pet Challenge Site, used to wire functionality together.
	Text Domain: mpc-core
	Author: David A. Powers
	Version: 1.1.0
	Author URI: http://musicahermetica.com
	License: n/a
*/

/*
	Copyright 2016 David A. Powers / Pet Guardian
	This plugin is not intended for public distribution
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // exit if accessed directly!
}

require_once('mpc-config.php');
require_once('mpc-admin.php');
require_once('vendor/autoload.php');
require_once('classes/autoload.php');

// require_once('tests/microtest.php');
// require_once('tests/petownertest.php');
// require_once('tests/notificationtest.php');
// require_once('tests/twiliomessagetest.php');
// require_once('tests/messageactivitytest.php');
// require_once('tests/subsequentsubmissionstest.php');

class Mpc_Core {
	public $genericNotifyForms;

	public function __construct() {
		$this->genericNotifyForms = [];
		//run code for custom post type - NotificationPost
		$np = new NotificationPost();
		$this->setupNotificationFilters();
		$this->setupAddPetHooks();
		$this->setupPetfileHooks();
		$this->setupGuardianResponse();
		$this->setupTestForm();
		$this->setupFirstResponderForm();
		//
		$this->setupRestUrls();	
		$this->addGenericGuardianForms();
		$this->setupGenericGuardianForms();
	}
	public function setupAddPetHooks() {
		//Form 67: Add pets & Guardians
		$this->add_action( "gform_pre_submission_67", 'addPetsNotificationLogic', 3 );
	}
	public function setupPetfileHooks() {
		$arr = array('6','57','58','59','60');
		$i = 1;
		foreach ($arr as $a) {
			$str = "gform_pre_submission_{$a}";
			$action = "onSubmitPetfile{$i}";
			$this->add_action( $str, $action, 1 );
			$i++;
		}
	}
	public function setupNotificationFilters() {
		//Form 65: Registration
		$this->addFilter( "gform_notification_65", 'filterRegistrationNotification', 3 );
		//Form 67: Add pets & Guardians
		$this->addFilter( "gform_notification_67", 'filterAddPetsNotification', 3 );
		//Form 64: Guardian Response
		$this->addFilter( "gform_notification_64", 'filterGuardianResponseNotification', 3 );
		//PETFILE 1
		$this->addFilter( "gform_notification_6", 'filterPetfile1Notification', 3 );
		//PETFILE 2
		$this->addFilter( "gform_notification_57", 'filterPetfile2Notification', 3 );
		//PETFILE 3
		$this->addFilter( "gform_notification_58", 'filterPetfile3Notification', 3 );
		//PETFILE 4
		$this->addFilter( "gform_notification_59", 'filterPetfile4Notification', 3 );
		//PETFILE 5
		$this->addFilter( "gform_notification_60", 'filterPetfile5Notification', 3 );
		//$this->add_filter( "gform_notification_6", $this, 'petfile1_notification', 10, 3 );

	}
	public function filterPetfile1Notification($notification, $form, $entry) {
		return $this->filterPetfileNotification($notification,$entry,1);
	}
	public function filterPetfile2Notification($notification, $form, $entry) {
		return $this->filterPetfileNotification($notification,$entry,2);
	}
	public function filterPetfile3Notification($notification, $form, $entry) {
		return $this->filterPetfileNotification($notification,$entry,3);
	}
	public function filterPetfile4Notification($notification, $form, $entry) {
		return $this->filterPetfileNotification($notification,$entry,4);
	}
	public function filterPetfile5Notification($notification, $form, $entry) {
		return $this->filterPetfileNotification($notification,$entry,5);
	}			
	public function filterPetfileNotification($notification,$entry,$petNum) {
		$data = Petfile::petfilePost2Data($petNum,$_POST);
		$guardian = GuardianHelper::findNotificationGuardian($notification['name'],$data);
		$message = MessageActivity::createGuardianEmail($notification,$guardian);
		$user = wp_get_current_user();
		$meta = get_metadata('user', $user->ID);
		$notificationMeta = $message->addNotification($user->ID,$meta);
		return $notification;
	}
	public function filterRegistrationNotification($notification, $form, $entry) {
		$toField = $notification['to'];
		$to = $entry[$toField];
		
		$user = get_user_by( "email", $entry['2'] );
		$meta = get_metadata('user', $user->ID);
		$message = new MessageActivity($notification['name'],'email',$to,$notification['id']);
		$notificationMeta = $message->addNotification($user->ID,$meta);

		return $notification;
	}
	public function filterAddPetsNotification($notification, $form, $entry ) {		
		$toField = $notification['to'];
		$to = $entry[$toField];
		$user = wp_get_current_user();
		$meta = get_metadata('user', $user->ID);
		$message = new MessageActivity($notification['name'],'email',$to,$notification['id']);
		$notificationMeta = $message->addNotification($user->ID,$meta);
		return $notification;
	}
	public function filterGuardianResponseNotification($notification, $form, $entry ) {
		$toField = $notification['to'];
		$to = $entry[$toField];
		$user = get_user_by( "email", $entry['9'] );
		$meta = get_metadata('user', $user->ID);
		$message = new MessageActivity($notification['name'],'email',$to,$notification['id']);
		$notificationMeta = $message->addNotification($user->ID,$meta);

		return $notification;
 	}
	public function addPetsNotificationLogic($form) {
		$user = wp_get_current_user();
		$meta = get_metadata('user', $user->ID);
		$pet_owner_id = SubsequentSubmissions::petOwnerId($meta);

		$data = Petfile::addPetsPost2Data($_POST);
		$numPets = $data['how_many_pets_owned'];
		
		$pets = [];
		$notifications = [];
		
		for($petNum=1; $petNum<($numPets+1); $petNum++) {
			if(SubsequentSubmissions::isPetNew($petNum, $meta)) {
				//echo "We got one!";
				//if new pet, add guardians request to send list
			
				$numGuardians = $data["num_guardians_p$petNum"]; //find all guardians
				
				for($g=1; $g<$numGuardians+1;$g++) {
					$notifications[] = SubsequentSubmissions::createNotification('request',$petNum,$g);
				}
								
			} else {
				//add pets to array for later
				$pets[] = new Pet( $petNum, $pet_owner_id, $meta );
			}
		}
		//now we take care of pets that aren't new
		foreach($pets as $pet) {				
			$newPet = Petfile::getPet($pet->petNum,$data);
			$notifications = SubsequentSubmissions::writeAddPetsNotifications($pet,$newPet,$notifications);
		}

		$json = json_encode($notifications);
		$_POST['input_239'] = $json;
		// mail('davenewcomb+f4d@gmail.com', 'add pets ->pets', $json);
	}

	public function onSubmitPetfile1() {
		Mpc_Core::petfileNotificationLogic(1);
	}
	public function onSubmitPetfile2() {
		Mpc_Core::petfileNotificationLogic(2);
	}
	public function onSubmitPetfile3() {
		Mpc_Core::petfileNotificationLogic(3);
	}
	public function onSubmitPetfile4() {
		Mpc_Core::petfileNotificationLogic(4);
	}
	public function onSubmitPetfile5() {
		Mpc_Core::petfileNotificationLogic(5);
	}

	public function petfileNotificationLogic($petNum) {
		$user = wp_get_current_user();
		$meta = get_metadata('user', $user->ID);
		$pet_owner_id = SubsequentSubmissions::petOwnerId($meta);
		$pet = new Pet( $petNum, $pet_owner_id, $meta );
		//* $petDebug = print_r($pet, true);
		//* mail('davenewcomb+f4d@gmail.com','petfile $pet obj',$petDebug);
		$data = Petfile::petfilePost2Data($petNum,$_POST);
		$newPet = Petfile::getPet($pet->petNum,$data);
		$notifications = SubsequentSubmissions::writeAddPetsNotifications($pet,$newPet,[]);
		$json = json_encode($notifications);
		$_POST['input_205'] = $json;
		//* mail('davenewcomb+f4d@gmail.com', 'petfileNotificationLogic', $json);
	}

	public function addGenericGuardianForms() {
		//$formId,$ownerFieldId,$confirmationFieldId,$guardianNotificationTemplate
		$this->addGenericForm('66','12','10','1985');
		//$formId,$ownerFieldId,$confirmationFieldId,$guardianNotificationTemplate,$ownerNotificationTemplate
		$this->addGenericForm('66','11','12','1978','1976');
	}

	public function addGenericForm($formId,$ownerFieldId,$confirmationFieldId,$notificationTemplate) {
		$gfn = new GenericFormNotification($formId,$ownerFieldId,$confirmationFieldId,$notificationTemplate);
		$this->genericNotifyForms[] = $gfn;
	}
	public function setupGenericGuardianForms() {
		foreach ($this->genericNotifyForms as $form) {
			$formfilterStr = "gform_pre_submission_{$form->formId}";
			$this->add_action( $formfilterStr, 'filterGenericNotificationForm' );
			$filterStr = "gform_confirmation_{$form->formId}";
			$this->add_action( $filterStr, 'filterGenericNotificationConfirmation', 3 );
		}
	}
	public function filterGenericNotificationForm($form) {
		$form = $this->genericNotifyForms[0];
		$this->genericGuardianNotifications($form->ownerFieldId,$form->confirmationFieldId,$form->notificationTemplate);
	}
	public function filterGenericNotificationConfirmation($confirmation,$form,$entry) {
		$form = $this->genericNotifyForms[0];
		$confirmation = $entry[$form->confirmationFieldId];
		return $confirmation;
	}
	public function setupGuardianResponse() {
		if ($_SERVER['SERVER_NAME']==="localhost") {$formId = '';}
		else {$formId = Mpc_Config::GUARDIAN_RESPONSE_FORM_ID; }
		$gformStr = "gform_after_submission_{$formId}";
		$this->add_action( $gformStr, 'updateAfterGuardianResponse' );		
		$this->add_action( $gformStr, 'logGuardianResponseOwnerNotification', 3 );
	}

	public function updateAfterGuardianResponse($entry) {
		//get user email
    $pg_email = $entry[Mpc_Config::GRD_RESP_OWNER_EMAIL_FIELD_ID];
    $pg_user_query = new WP_User_Query(array('search' => $pg_email, 'search_columns' => array('user_email')));
    $pg_users = $pg_user_query->get_results();
    // Check for results.
    if (!empty($pg_users)) {
      $user = $pg_users[0];        
	    $gnum = rgar($entry, Mpc_Config::GRD_RESP_GNUM_FIELD_ID);
	    $pnum = rgar($entry, Mpc_Config::GRD_RESP_PNUM_FIELD_ID);	  
	    $meta = array(
	        'prefix'         => rgar($entry, Mpc_Config::GRD_RESP_PREFIX_FIELD_ID), 
	        'first_name'     => rgar($entry, Mpc_Config::GRD_RESP_FIRST_NAME_FIELD_ID), 
	        'last_name'      => rgar($entry, Mpc_Config::GRD_RESP_LAST_NAME_FIELD_ID), 
	        'email'          => rgar($entry, Mpc_Config::GRD_RESP_EMAIL_FIELD_ID), 
	        'mobile_phone'   => rgar($entry, Mpc_Config::GRD_RESP_MOBILE_PHONE_FIELD_ID), 
	        'response'   => rgar($entry, Mpc_Config::GRD_RESP_RESPONSE_FIELD_ID), 
	       );
	    $guardian = new Guardian( $meta, 0, $pnum, $gnum, '', '');
			UserHelper::updateGuardianMeta($user->ID,$guardian);
    } else {
	  	// There were no users found with this email.
        //echo '<p>Something is wrong here, we did not find a Pet Owner to update with your response. Please email support and let us know <a href="mailto:support@millionpetchallenge.com?subject=Error Updating Pet Owner with Pet Guardian Response">Email Us</a></p>';
    }
	}


	public function setupTestForm() {
		//test code
		if ($_SERVER['SERVER_NAME']==="localhost") {$formId = '1';}
		else {$formId = Mpc_Config::TEST_FORM_ID; }
		$gformStr = "gform_pre_submission_{$formId}";
		$this->add_action( $gformStr, 'runTests' );		
	}
	public function setupFirstResponderForm() {
		//first responder code
		$firstRespFormId = Mpc_Config::FR_FORM_ID; 
		$firstRespString = "gform_pre_submission_{$firstRespFormId}";
		$filterStr = "gform_confirmation_{$firstRespFormId}";
		$this->add_action( $firstRespString, 'filterFirstResponder' );
		$this->add_action( $filterStr, 'filterConfirmation', 3 );
	}
	public function setupRestUrls() {
		//Set the phone number log update
		//view phone log @ http://petguardian.staging.wpengine.com/wp-admin/admin.php?page=gf_entries&id=68
		//http://petguardian.staging.wpengine.com/wp-json/petguardian/v1/twilio-response
		add_action( 'rest_api_init', function () {
		  register_rest_route( 'petguardian/v1', '/twilio-response', array(
		        'methods' => 'POST',
		        'callback' => array('PhoneNumber','smsCallback')
		  ));
		  //http://petguardian.staging.wpengine.com/wp-json/petguardian/v1/ivr-notification?lookup=9647665452
		  //http://petguardian.staging.wpengine.com/wp-json/petguardian/v1/sms-notification?lookup=1150860946&from=17736092730
			Mpc_Core::registerUrl('/ivr-notification','GET',array($this,'ivrNotification'));
			Mpc_Core::registerUrl('/sms-notification','GET',[$this,'smsNotification']);
		} );			
	}
	public function filterConfirmation($confirmation,$form,$entry) {
		$confirmation = $entry[Mpc_Config::FR_FORM_CONFIRMATION_FIELD_ID];
		return $confirmation;
	}
	public function filterFirstResponder($form) {
		$id = $this->getPetOrOwnerId(Mpc_Config::FR_FORM_OWNER_FIELD_ID);
		$owner = new PetOwner($id);
		//if user not valid
		if($owner->user===false) {
			Confirmation::createConfirmation("Invalid Pet Owner ID submitted. No alerts have been sent, please verify the pet owner ID number and try again.");
		} else {
			$confirmation = TwilioMessage::alertPrimary($owner,Mpc_Core::getFirstResponderOwnerTemplate());
			if (PetOwner::isValidPetId($id)) {
				$petNum = substr($id , 0, 1);
				$confirmation .= TwilioMessage::alertGuardiansForPet($owner,$petNum,Mpc_Core::getFirstResponderGuardianTemplate());
			} else {
				$confirmation .= TwilioMessage::alertGuardians($owner,Mpc_Core::getFirstResponderGuardianTemplate());
			}
			Confirmation::createConfirmation($confirmation);
		}
	}
	public function genericGuardianNotifications($ownerFieldId,$confirmationFieldId,$notificationTemplate,$ownerNotificationTemplate="") {
		$id = $this->getPetOrOwnerId($ownerFieldId);
		$owner = new PetOwner($id);
		$confirmation = '';
		//if user not valid
		if($owner->user===false) {
			$confirmation .= "Invalid Pet Owner ID submitted. No alerts have been sent, please verify the pet owner ID number and try again.";
		} else {
			$confirmation = TwilioMessage::alertPrimary($owner,$ownerNotificationTemplate);
			if (PetOwner::isValidPetId($id)) {
				$petNum = substr($id , 0, 1);
				$confirmation .= TwilioMessage::alertGuardiansForPet($owner,$petNum,$notificationTemplate);
			} else {
				$confirmation .= TwilioMessage::alertGuardians($owner,$notificationTemplate);
			}
		}
		Confirmation::createGenericConfirmation($confirmation,$confirmationFieldId);
	}		
	public function ivrNotification() {
		$this->phoneSysNotification(Mpc_Config::FR_IVR_OWNER_NOTIFICATION_ID,
			Mpc_Config::FR_IVR_GUARD_NOTIFICATION_ID);
	}
	public function smsNotification() {
		$this->phoneSysNotification(Mpc_Config::FR_SMS_OWNER_NOTIFICATION_ID,
			Mpc_Config::FR_SMS_GUARD_NOTIFICATION_ID);
	}
	public function phoneSysNotification($ownerId,$guardianId) {
		$lookup = $_GET['lookup'];
		$owner = new PetOwner($lookup);
		$from = $_GET['from'];
		//if user valid
		if($owner->user===false) {
			//log "Invalid Pet Owner ID submitted.";
		} else {
			$confirmation = TwilioMessage::alertPrimary($owner,$ownerId,$from);
			if (PetOwner::isValidPetId($lookup)) {
				$petNum = substr($lookup , 0, 1);
				$confirmation .= TwilioMessage::alertGuardiansForPet($owner,$petNum,$guardianId,$from);
			} else {
				$confirmation .= TwilioMessage::alertGuardians($owner,$guardianId,$from);
			}
			//log Confirmation::createConfirmation($confirmation);
			echo $confirmation;
		}
	}
	public function add_action($action,$method, $args=1) {
		add_action( $action, [$this, $method], 100, $args );
	}
	public function addFilter($action,$method,$args=1) {
		add_filter( $action, [$this, $method], 100, $args );
	}
	public function runTests($form) {
		$test = new TwilioMessageTest();
		echo $test->log;		
		/*
		//$test = new SubsequentSubmissionsTest();
		//echo $test->log;
		
		echo $test->log;
		$test = new NotificationTest();
		echo $test->log;		
		*/
		exit();
	}

	public function registerUrl($url,$method,$callbackUrl) {
		register_rest_route( 'petguardian/v1', $url, array(
			'methods' => $method,
			'callback' => $callbackUrl
		));
	}


	public function getPetOrOwnerId($fieldId) {
		$key = 'input_'.$fieldId;
		return rgar($_POST,$key);

	}
	public function invalidUser() {
		return 0;
	}	
	static public function getFirstResponderOwnerTemplate() {
		if ($_SERVER['SERVER_NAME']==="localhost") {return '8';}
		else {return Mpc_Config::FR_FORM_OWNER_NOTIFICATION_ID;; } 
	}
	static public function getFirstResponderGuardianTemplate() {
		if ($_SERVER['SERVER_NAME']==="localhost") {return '7';}
		else {return Mpc_Config::FR_FORM_GUARD_NOTIFICATION_ID; } 
	}

}
new Mpc_Core();
