<?php
class SubsequentSubmissions {

	public function __construct($meta) {
		$this->messageActivity = '';


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