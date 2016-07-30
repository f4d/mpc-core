<?php
class UserHelper {
	const PRIMARY_NUM_KEY = "mobile_phone";
	static public function findUser($pet_owner_id) {
		$user = false;
		$query = new WP_User_Query( array( 'meta_key' => 'pet_owner_id', 'meta_value' => $pet_owner_id ) );
		if (count($query->results) == 1) {
			$user = $query->results[0];
		}
		return $user;
	}
	static public function debugMeta($userId) {
		
	}
	static public function guardianResponseKey($petNum,$guardianNum) {
		return "p{$petNum}_guardian_{$guardianNum}_response";
	}
	static public function guardianMobileKey($petNum,$guardianNum) {
		return "p{$petNum}_guardian_{$guardianNum}_mobile_phone";
	}
	static public function getGuardianNumber($userId,$petNum,$guardianNum) {
		$meta = get_metadata('user', $userId);
		$key = UserHelper::guardianMobileKey($petNum,$guardianNum);
		return $meta[$key][0];
	}
	static public function updateGuardianNumber($userId,$petNum,$guardianNum,$newNum) {
		$key = UserHelper::guardianMobileKey($petNum,$guardianNum);
		update_user_meta( $userId, $key, $newNum );
		return UserHelper::getGuardianNumber($userId,$petNum,$guardianNum);
	}
	static public function markGuardianAccepted($userId,$petNum,$guardianNum) {
		$key = UserHelper::guardianResponseKey($petNum,$guardianNum);
		update_user_meta( $userId, $key, 1 );
	}
	static public function markGuardianDeclined($userId,$petNum,$guardianNum) {
		$key = UserHelper::guardianResponseKey($petNum,$guardianNum);
		update_user_meta( $userId, $key, 0 );
	}
	static public function updatePrimaryNumber($userId,$newNum) {
		$key = UserHelper::PRIMARY_NUM_KEY;
		update_user_meta( $userId, $key, $newNum );
	}
	static public function updateNumbers($userId,$meta,$oldNum,$newNum) {
		//check primary number
		$primary = PhoneNumber::cleanup($meta[UserHelper::PRIMARY_NUM_KEY]);
		if($primary == $oldNum) {
			UserHelper::updatePrimaryNumber($userId,$newNum);
		}
		$numPets = Pet::numOfPets($meta);
		for($i=1;$i<($numPets+1);$i++) {
			for($j=1;$j<6;$j++) {
				$field = UserHelper::guardianMobileKey($i,$j);
				$currentNum = PhoneNumber::cleanup($meta[$field]);
				//if the number matches, update it
				if($currentNum == $oldNum) {
					UserHelper::updateGuardianNumber($userId,$i,$j,$newNum);
				}
			}
		}		
	}
}
