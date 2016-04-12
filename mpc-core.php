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
require_once('classes.php');
require_once('vendor/autoload.php');
require_once('classes/twiliohelper.php');
require_once('classes/petowner.php');
require_once('classes/pet.php');
require_once('classes/guardian.php');
require_once('classes/notification.php');
require_once('tests/microtest.php');
require_once('tests/petownertest.php');
require_once('tests/notificationtest.php');

class Mpc_Core {
	public function __construct() {
		$formId = '1';
		$gformStr = "gform_pre_submission_{$formId}";
		$this->add_action( $gformStr, 'filterGform' );
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
		//TwilioHelper::sendToGroup('Boo!',['7736092730','7736411561'],
		//	'http://petguardian.staging.wpengine.com/wp-json/petguardian/v1/twilio-response');
	}
	public function filterGform($form) {
		$test = new PetOwnerTest();
		echo $test->log;
		$test = new NotificationTest();
		echo $test->log;		
		exit();
	}
	static public function alertGuardians($pets,$userId) {
		$alerts = new StdClass;
		$alerts->sent = 0;
		$alerts->total = 0;
		foreach($pets as $p) {
			$gNum = 1;
			foreach($p->guardians as $g) {
				if ( $g->mobile_phone == '' || $g->mobile_phone == '_____' ) {
					//skip
				} else {
					//check the number, if it's new, save to the db
					$phoneNumber = PhoneNumber::lookup($g->mobile_phone,$userId);
					if($g->response==='1' && $phoneNumber->health != "bad") {
						$alerts->sent++;
						$alerts->total++;
						try {
						  TwilioHelper::sendMsg($p->msg,$phoneNumber->number);
						} catch (Exception $e) {
							$alerts->sent--;
							//mark number as bad, update user meta, send emails
							PhoneNumber::updateNumberHealth($phoneNumber->number,'failed'); 
							UserHelper::updateGuardianNumber($userId,$p->petfile,$gNum,'___');
							mail ( 'admin@petguardianinc.com' , 'Bad Number: Pet Guardian' , $e->getMessage() );
						}
					} else {
						//don't send to an invalid number
					}
					$gNum++;					
				}

			}
		}
		$alerts->failed = $alerts->total - $alerts->sent;
		return $alerts;
	}
}
new Mpc_Core();