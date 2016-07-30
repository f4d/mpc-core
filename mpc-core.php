<?php
/*
	Plugin Name: MPC Core
	Plugin URI: http://musicahermetica.com
	Description: Core plugin for Million Pet Challenge Site, used to wire functionality together.
	Text Domain: mpc-core
	Author: David A. Powers
	Version: 1.0.0
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

require_once('tests/microtest.php');
require_once('tests/petownertest.php');
require_once('tests/notificationtest.php');
require_once('tests/twiliomessagetest.php');

class Mpc_Core {
	public $genericNotifyForms;

	public function __construct() {
		$this->genericNotifyForms = [];
		//run code for custom post type - NotificationPost
		$np = new NotificationPost();
		$this->setupTestForm();
		$this->setupFirstResponderForm();
		$this->setupRestUrls();	
		$this->addGenericGuardianForms();
		$this->setupGenericGuardianForms();
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
	}
	public function createPetGuardArr($guardNum,$petNum) {
		$suffix = array('prefix','first_name','last_name','email','response','mobile_phone');
		$str = "p{$petNum}_guardian_{$guardNum}_";
		$arr = array();
		foreach($suffix as $s) {
			array_push($arr,($str.$s));
		}
		return $arr;
	} 
	function updateAfterGuardianResponse($entry) {
    // Get user email from $entry (gravity form 64 entry object).
    $pg_email = $entry['9'];
  
    // Make WP_User_Query object to search for the user with this email.
    $pg_user_query = new WP_User_Query(array('search' => $pg_email, 'search_columns' => array('user_email')));

    // Get the results from the WP_User_Query object.
    $pg_users = $pg_user_query->get_results();

    // Check for results.
    if (!empty($pg_users)) {
        $pg_user = $pg_users[0];        
        $pg_user_id = $pg_user->ID;
	  
	  	$pg_response_string = rgar($entry, '15');
	  
	  	// The response field is '1' => $pg_response_number = '1', else if 'Declined' => $pg_response_number = '0'.
	  	// $pg_response_number = ($pg_response_string == 'Accepted' ? 1 : 0);
		// Old Behavior ^	  
	  
	  	// The response field now is either '0' or '1' for declined and accepted, respectively. Updating value of number accordingly. 
	  	$pg_response_number = ($pg_response_string === '1' ? '1' : '0');

        // Step 2. 
        $pg_meta_from_petguardian_response = array(
            'pg_prefix'         => rgar($entry, '10.2'), 
            'pg_first_name'     => rgar($entry, '10.3'), 
            'pg_last_name'      => rgar($entry, '10.6'), 
            'pg_email'          => rgar($entry, '5'), 
		  	'pg_response'       => $pg_response_number,
            'pg_mobile_phone'   => rgar($entry, '12'), 
            );

        // Step 3.
        $pg_petguardian_number = rgar($entry, '13');
        $pg_pet_number = rgar($entry, '14');

        $pg_pet_and_petguardian_numbers = createPetGuardArr($pg_petguardian_number, $pg_pet_number);
        // Assuming that it is the first pet and the first pet guardian, we proceed to update the user meta for 
	  	// these meta fields. 
       for ($x = 0; $x < sizeof($pg_pet_and_petguardian_numbers); $x++) {
           $pg_meta_value_to_update = $pg_meta_from_petguardian_response[array_keys($pg_meta_from_petguardian_response)[$x]];
           update_user_meta($pg_user_id, $pg_pet_and_petguardian_numbers[$x], $pg_meta_value_to_update);
       }

    } else {
	  	// There were no users found with this email.
        echo 'Something is wrong here, we did not find a Pet Owner to update with your response. Please email support and let us know <a href="mailto:support@millionpetchallenge.com?subject=Error Updating Pet Owner with Pet Guardian Response">Email Us</a>';
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
	public function runTests($form) {
		$test = new PetOwnerTest();
		echo $test->log;
		$test = new NotificationTest();
		echo $test->log;		
		$test = new TwilioMessageTest();

		echo $test->log;		
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