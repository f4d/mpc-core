<?php
class SubsequentSubmissions {

	public function __construct($meta) {
		$this->messageActivity = '';


	}

	/**
	 * getNotificationStr generates correct data
	 * based on the user meta messageActivity and the formTyoe
	 * formTypes: 
	 * 'registration' (65)
	 * 'add-pets' (67)
	 * 'petfile' (6,57,58,59,60)
	 * 
	 * @param  string $formtype ('registration','add-pets', or 'petfile')
	 * @return n/a
	 */	
	public function getNotificationStr($formType) {

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
		$pet_owner_id = SubsequentSubmissions::petOwnerId($meta);	
		//get the number of pets from form, NOT meta!
		$numPets = (int) rgar($_POST,'input_59');
		$pets = array();
		$killArr = [];
		for($i=1;$i<($numPets+1);$i++) {
			if(SubsequentSubmissions::isPetNew($i,$meta)) {
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


	static public function isPetNew($petnum,$meta) {
		$metaname = SubsequentSubmissions::meta($meta,"pet_{$petnum}_name");
		$numberPets = intval(SubsequentSubmissions::numberOfPets($meta));
		//is pet name in the user meta? and meta number of pets greater
		return ($metaname=="") && ($petnum > $numberPets);
	}
	static public function numberGuardians($petNum,$meta) {
		for($g = 1; $g < 6; $g++) {
			$str = "p1_guardian_$g_email";
			$email = SubsequentSubmissions::meta($meta,'how_many_pets_owned');
			if($email=='') {return $g-1;}
		}
		return $g;
	}

	static public function numberOfPets($meta) {
		return SubsequentSubmissions::meta($meta,'how_many_pets_owned');
	}
	/**
	 * petOwnerId finds the pet owner id when given the user meta data 
	 * @param  array $meta   An array containing the WP user meta data
	 * @return string        Returns pet owner id
	 */
	static public function petOwnerId($meta) {
		return rgar(rgar($meta,'pet_owner_id'),0);
	}
	/**
	 * meta() allows one to retrieve an arbitrary field of user meta data
	 * @param  array $meta     An array containing the WP user meta data
	 * @param  string $field   Key for user meta data 
	 * @return string          Returns value of user meta if $field exists
	 */
	static public function meta($meta,$field) {
		return rgar(rgar($meta,$field),0);
	}	
	static public function createNotification($type,$petNum,$gNum) {
		return "$type-p{$petNum}g{$gNum}";
	}
	/**
	 * checkGuardianNotifications is creates a list of sent notifications
	 * from two versions of pet data, one from meta, and one from a form.
	 * This function is used when forms are submitted that update
	 * guardian information, for a pet that is not new.
	 * @param  Pet object $pet      (created from meta)
	 * @param  Pet2 object $newPet  (created from form data)
	 * @param  array $killArr       (list of blocked notifications)
	 * @return array                (updated list of blocked notifications)
	 */	
	static public function writeAddPetsNotifications($pet,$newPet,$notifications) {
		
		$stop = $newPet->howManyGuardians() + 1;
		for($g=1; $g<$stop; $g++) {
			$savedGuardian = $pet->guardians[$g];
			if($savedGuardian->email != "") {
				if($savedGuardian->response=="") {
					//Add new guardian reminder 
					$notifications[] = SubsequentSubmissions::createNotification('reminder',$pet->petNum,$g);
				} else {
					//do nothing
					//they have responded
				}
			} else {
				//if they there is a guardian email in form but not meta, guardian is new			
				$newGuardian = $newPet->guardians[($g)];
				if ($newGuardian->email != '') {
					//Send guardian request
					$notifications[] = SubsequentSubmissions::createNotification('request',$pet->petNum,$g);
				}
			}				
		}
		return $notifications;		
	}		
}