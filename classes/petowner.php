<?php 
public class PetOwner {
	public $user;
	public function __construct($key) {
		// 9647665452 example pet owner ID
		if (count(preg_match("/9\d{9}/")>0) {
			$this->user = PetOwner::find($key);
		// 1274276602 example pet ID
		} elseif (count(preg_match("\d{10}/")>0) {
			$this->user = PetOwner::findByPetId($key);
		} else {
			$this->user = PetOwner::findByEmail($key);
		}
		return $this->user;
	}
	public static function find($petOwnerId) {
		$user = false;
		$query = new WP_User_Query( array( 'meta_key' => 'pet_owner_id', 'meta_value' => $petOwnerId ) );
		if (count($query->results) == 1) {
			$user = $query->results[0];
		}
		return $user;
	}
	public static function findByPetId($petId) {
		$user = false;
		for($i=1;$i<6;$i++) {
			if (count(preg_match("/".$i."\d{9}/", $input_line, $output_array)>0) {
				$query = new WP_User_Query( array( 'meta_key' => "pet_{$i}_name", 'meta_value' => $petId ) );
				if (count($query->results) == 1) {
					$user = $query->results[0];
					break;
				}
			}
		}
		return $user;
	}
	public static function findByEmail($email) {
		$query = new WP_User_Query( array( 'meta_key' => 'user_email', 'meta_value' => $email ) );
		if (count($query->results) == 1) {
			$user = $query->results[0];
		}
		return $user;
	}
}