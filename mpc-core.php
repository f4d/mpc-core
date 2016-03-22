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
class Mpc_Core {
	public function __construct() {
		//$d = new Dummy('U h4v3 git class!!!'); 
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