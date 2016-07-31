<?php
class Mpc_Config {
	//Guardian Response Form
	const GUARDIAN_RESPONSE_FORM_ID = '64'; //Guardian Response, Form Id
	const GRD_RESP_OWNER_EMAIL_FIELD_ID = '9'; //Pet Owner Email, Field Id
	const GRD_RESP_GNUM_FIELD_ID = '13'; //Guardian Number, Field Id
	const GRD_RESP_PNUM_FIELD_ID = '14'; //Petfile Number, Field Id
	const GRD_RESP_PREFIX_FIELD_ID = '10.2'; //Guardian Prefix, Field Id
	const GRD_RESP_FIRST_NAME_FIELD_ID = '10.3'; //Guardian First Name, Field Id
	const GRD_RESP_LAST_NAME_FIELD_ID = '10.6'; //Guardian Last Name, Field Id
	const GRD_RESP_EMAIL_FIELD_ID = '5'; //Guardian Email, Field Id
	const GRD_RESP_MOBILE_PHONE_FIELD_ID = '12'; //Guardian Mobile, Field Id
	const GRD_RESP_RESPONSE_FIELD_ID = '15'; //Guardian Response, Field Id

/*
			$pg_email = $entry['9'];
	    $gnum = rgar($entry, '13');
	    $pnum = rgar($entry, '14');	  
      'prefix'         => rgar($entry, '10.2'), 
      'first_name'     => rgar($entry, '10.3'), 
      'last_name'      => rgar($entry, '10.6'), 
      'email'          => rgar($entry, '5'), 
      'mobile_phone'   => rgar($entry, '12'), 
      'response'   => rgar($entry, '15'), 
 */

	//First Responder form config values
	const FR_FORM_ID = '62'; //First Responder Form ID
	const FR_FORM_OWNER_FIELD_ID = '11'; //Field ID, for PetOwnerID or PetID lookup
	const FR_FORM_CONFIRMATION_FIELD_ID = '12'; //Field ID, of a hidden field where confirmation message is stored
	const FR_FORM_OWNER_NOTIFICATION_ID = '1976'; //Notification Post ID, for message sent to owner
	const FR_FORM_GUARD_NOTIFICATION_ID = '1977'; //Notification Post ID, for message sent to guardians
	
	//First Responder IVR config values
	const FR_IVR_OWNER_NOTIFICATION_ID = '1979'; //Notification Post ID, for message sent to owner, from IVR
	const FR_IVR_GUARD_NOTIFICATION_ID = '1987'; //Notification Post ID, for message sent to guardians, from IVR

	//First Responder SMS config values
	const FR_SMS_OWNER_NOTIFICATION_ID = '1981'; //Notification Post ID, for message sent to owner, from SMS
	const FR_SMS_GUARD_NOTIFICATION_ID = '1980'; //Notification Post ID, for message sent to guardians, from SMS
	
	//unit & integration tests
	const TEST_FORM_ID = '69'; //ID of Form to use for Unit / Integration Tests
}