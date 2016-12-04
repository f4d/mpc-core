<?php

/**
 * SubmissionsHelper class provides some simple utilities for getting meta
 * data, including checking number of pets, and whether a pet is new.
 */
class SubmissionsHelper {
	/**
	 * numberPets finds the number of pets when given the user meta data 
	 * @param  array $meta   An array containing the WP user meta data
	 * @return string        Returns value of how_many_pets_owned user meta
	 */
	static public function numberPets($meta) {
		return SubmissionsHelper::meta($meta,'how_many_pets_owned');
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
	/**
	 * isPetNew uses the pet name field in user meta to check if pet exists.
	 * @param  string $petnum     Which pet is it? (1-5)
	 * @param  array $meta        An array containing the WP user meta data
	 * @return boolean
	 */
	static public function isPetNew($petnum,$meta) {
		$metaname = SubmissionsHelper::meta($meta,"pet_{$petnum}_name");
		//is pet name in the user meta?
		return ($metaname=="");
	}

}