<?php
class Mpc_Admin {

/** the class constructor   */
	public function __construct() {
		if ( is_admin() ){ 
			add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
			add_action( 'admin_init',  array( $this, 'plugin_admin_init' ) );
		}
	}

	public function plugin_menu() {
		//add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		add_options_page( 'Mpc Core', 'Mpc Core', 'manage_options', 'mpc-core', array( $this, 'plugin_options' ) );
	}	
	public function plugin_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		} 
		$str = '<div class="wrap">';
    $str .= do_settings_sections('mpc-core'); 
    $str .= '</div>';
		return $str;
	}	
	public function plugin_admin_init(){
		//add_settings_section ( string $id, string $title, callable $callback, string $page )
		add_settings_section('plugin_main', 'Main Settings', array($this,'plugin_section_text'), 'mpc-core');
	}
	public function plugin_section_text() {
		$str = <<<EOT
<h3>Mpc Core Instructions</h3>

<h4>Notification Template Vars</h4>
<h5>Owner</h5>
<p>{{petowner.firstname}} {{petowner.lastname}}</p>

<h5>Guardian</h5>
<p>{{guardian.prefix}} {{guardian.first_name}} {{guardian.last_name}} {{guardian.email}} {{guardian.mobile_phone}} {{guardian.petId}} {{guardian.petname}} {{guardian.pettype}} {{guardian.petfileUrl}}</p>

<h5>Form Fields</h5>
<p>Use the Gravity Form ID: {{post.1}} {{post.2}} etc...</p>

<h5>SMS / IVR</h5>

<p>Use {{from}} to get the caller ID</p>

<h4>First Responder Configuration</h4>
<p>To configure the first responder form settings, adjust the following lines 
in mpc-core.php, in the main folder of the mpc-core plugin:</p>
<ul>
<li>	const FR_FORM_ID = '62'; //First Responder Form ID
<li>	const FR_FORM_OWNER_FIELD_ID = '11'; //Field ID, for PetOwnerID or PetID lookup
<li>	const FR_FORM_CONFIRMATION_FIELD_ID = '12'; //Field ID, of a hidden field where confirmation message is stored
<li>	const FR_FORM_OWNER_NOTIFICATION_ID = '1976'; //Notification Post ID, for message sent to owner
<li>	const FR_FORM_GUARD_NOTIFICATION_ID = '1977'; //Notification Post ID, for message sent to guardians
</ul>

<ul>
<li>	const FR_IVR_OWNER_NOTIFICATION_ID = '1979'; //Notification Post ID, for message sent to owner, from IVR
<li>	const FR_IVR_GUARD_NOTIFICATION_ID = '1987'; //Notification Post ID, for message sent to guardians, from IVR
</ul>

<ul>
<li>	const FR_SMS_OWNER_NOTIFICATION_ID = '1979'; //Notification Post ID, for message sent to owner, from SMS
<li>	const FR_SMS_GUARD_NOTIFICATION_ID = '1987'; //Notification Post ID, for message sent to guardians, from SMS
</ul>

<h4>Test Form Configuration</h4>
<p>To configure the first responder form settings, adjust the following lines 
in mpc-core.php, in the main folder of the mpc-core plugin:</p>
<ul>
<li>	const TEST_FORM_ID = '69'; //ID of Form to use for Unit / Integration Tests
</ul>

<h4>Adding Additional Guardian Notification Forms</h4>
<ol>
<li>Create a new Gravity Form, with a field for PetOwner or Pet ID, and a hidden field for storing confirmations.</li>
<li>Create a new guardian Notification, with the text you want</li>
<li>Optionally, create a new owner Notification, with the text you want</li>
<li>Add a line in mpc-core, to the addGenericGuardianForms() function, like <b>\$this->addGenericForm('66','12','10','1985');</b>, where the 1st var is your form id, 2nd, lookup field id, 3rd, confirmation field id, and 4th, the post id of the new notification.</li>
<li>*OR: Add a line in mpc-core, to the addGenericGuardianForms() function, like <b>\$this->addGenericForm('66','11','12','1978','1976');</b>, where the 5th var is the (optional) post id of the new owner notification.</li>
</ol>
EOT;
		echo $str;
	}
}

$mpc_logs = new Mpc_Admin();