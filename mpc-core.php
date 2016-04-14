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

require_once('vendor/autoload.php');
require_once('classes/autoload.php');

require_once('tests/microtest.php');
require_once('tests/petownertest.php');
require_once('tests/notificationtest.php');
require_once('tests/twiliomessagetest.php');

class Mpc_Core {
	public function __construct() {
		//run code for custom post type - NotificationPost
		$np = new NotificationPost();
		//test code
		if ($_SERVER['SERVER_NAME']==="localhost") {$formId = '1';}
		else {$formId = '69'; }
		$gformStr = "gform_pre_submission_{$formId}";
		$this->add_action( $gformStr, 'runTests' );

		//$phoneFormId = '68';
		//first responder code
		$firstRespFormId = '62';
		$firstRespString = "gform_pre_submission_{$firstRespFormId}";
		$filterStr = "gform_confirmation_{$firstRespFormId}";
		//$this->add_action( $firstRespString, 'filterFirstResponder' );
		//$this->add_action( $firstRespString, 'filterConfirmation' );

		add_action( 'rest_api_init', function () {
		    register_rest_route( 'petguardian/v1', '/twilio-response', array(
		        'methods' => 'POST',
		        'callback' => array('PhoneNumber','smsCallback')
		    ) );
		} );		
	}
	public function filterConfirmation($confirmation,$form,$entry) {
		$confirmation = $entry['12'];
		return $confirmation;
	}
	public function filterFirstResponder($form) {
		$owner = new PetOwner($_POST['input_11']);
		//if user not valid
		if($owner->user===false) {
			TwilioHelper::createConfirmation("Invalid Pet Owner ID submitted. No alerts have been sent, please verify the pet owner ID number and try again.");
		} else {
			$confirmation = $this->alertPrimary($owner);
			$confirmation .= $this->alertGuardians($owner);
			TwilioHelper::createConfirmation($confirmation);
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
	public function alertPrimary($owner) {
		$n = new Notification($owner);
		$post = Notification::filterPost();
		if ($_SERVER['SERVER_NAME']==="localhost") {$templateId = '8';}
		else {$templateId = '1973'; }
		$template = Notification::getTemplate($templateId);
		$msg = $n->parseOwnerTemplate($template,$post);
		$message = new TwilioMessage($owner->phone,$msg,$owner);
		$message->send();
		if($message->result === -1) {
			$msg = "Warning: We were unable to send a message to the primary pet owner. ";
		} else {
			$msg = "Message sent to the primary pet owner. ";
		}
		return $msg;
	}
	public function alertGuardians($owner) {
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$guardians = $owner->getValidGuardians();
		$post = Notification::filterPost();
		$sent = [];
		foreach ($guardians as $guardian) {
			if ($_SERVER['SERVER_NAME']==="localhost") {$templateId = '7';}
			else {$templateId = '1974'; }
			$template = Notification::getTemplate($templateId);
			$msg = $n->parseGuardianTemplate($template,$post,$guardian);
			$message = new TwilioMessage($guardian->mobile_phone,$msg,$guardian);
			$message->send();
			$sent[] = $message;
		}
		$results = TwilioMessage::msgResults($sent);
		$msg = "We are attempting to send ".$results->attempted." messages to Pet Guardians. ";
		$msg .= "Warning: We were unable to send ".$results->failed." messages to Pet Guardians. ";
		return $msg;
	}
	private function invalidUser() {
		return 0;
	}	

}
new Mpc_Core();