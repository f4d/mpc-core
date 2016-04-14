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
		$formId = '1';
		$gformStr = "gform_pre_submission_{$formId}";
		$this->add_action( $gformStr, 'filterGform' );
		$np = new NotificationPost();
	}
	public function add_action($action,$method, $args=1) {
		add_action( $action, [$this, $method], 100, $args );
	}
	public function __archive($form) {
		/*
		$post = $_POST;
		$pet_owner_id = $post['input_11'];
		$user = UserHelper::findUser($pet_owner_id);
		//if user not valid
		if($user===false) {
			//$this->invalidUser();
		} else {
			//TwilioHelper::createAndSend($user,$pet_owner_id,$post);
		} */
	}
	public function filterGform($form) {
		//TwilioHelper::sendToGroup('Boo!',
		//	['7736092730','7736411561'],
		//	'http://petguardian.staging.wpengine.com/wp-json/petguardian/v1/twilio-response');
		$test = new PetOwnerTest();
		echo $test->log;
		$test = new NotificationTest();
		echo $test->log;		
		$test = new TwilioMessageTest();
		echo $test->log;		
		exit();
	}
	static public function alertGuardians($owner) {
		$alerts = new StdClass;
		$alerts->sent = 0;
		$alerts->total = 0;
		$owner = new PetOwner('cyborgk@gmail.com');

		$guardians = $owner->getValidGuardians();
		foreach($p->guardians as $g) {
			/*
			//check the number, if it's new, save to the db
			$phoneNumber = PhoneNumber::lookup($g->mobile_phone,$userId);
			if($g->response==='1' && $phoneNumber->health != "bad") {
				$alerts->sent++;
				$alerts->total++;
				
			} else {
				//don't send to an invalid number
			}
			$gNum++;*/
		}
		//$alerts->failed = $alerts->total - $alerts->sent;
		//return $alerts;
	}
}
new Mpc_Core();