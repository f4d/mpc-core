<?php
class Mpc_Config {
	
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