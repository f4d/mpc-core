<?php 
class PetOwner {
	public $user;
	public $data = [];
	public $numOfPets = 0;
	public $pets = [];
	public $petOwnerId = '9000000000';
	public $phone = '';
	public function __construct($key) {
		// 9647665452 example pet owner ID
		if (preg_match("/9\d{9}/",$key)===1) {
			$this->user = PetOwner::find($key);
		// 1274276602 example pet ID
		} elseif (preg_match("/\d{10}/",$key)===1) {
			$this->user = PetOwner::findByPetId($key);
		} else {
			$this->user = PetOwner::findByEmail($key);
		}
		if($this->user !== false) {
			//get the user metadata
			$this->_setVars();
		} else {
			$this->data = array();
			$this->numOfPets = 0;
		}
		return $this->user;
	}
	private function _setVars() {
			$this->data = get_metadata('user', $this->user->ID);
			$this->petOwnerId = PetOwner::getMetaVal($this->data,'pet_owner_id');
			$this->phone = PetOwner::getMetaVal($this->data,'mobile_phone');
			$this->numOfPets = PetOwner::numOfPets($this->data);
			for($i=1;$i<($this->numOfPets+1);$i++) {
				$this->pets[$i] = new Pet($i,$this->petOwnerId,$this->data);
				//$pets[$i]->msg = TwilioHelper::createMessage($post,$pets[$i]);
			}

	}
	public function getAllGuardians() {
		$g = [];
		for($i=1;$i<($this->numOfPets+1);$i++) {
			$g = array_merge($g,$this->pets[$i]->guardians);
		}
		return $g;
	}
	public function getValidGuardians() {
		$g = [];
		$all = $this->getAllGuardians();
		foreach ($all as $guardian) {
			if($guardian->response==="1") {
				$g[] = $guardian;
			}
		}
		return $g;
	}
	public static function getMetaVal($data,$key) {
		$q = rgar($data, $key);
		return rgar($q, 0);
	}
	public static function numOfPets($data) {
		$pets = rgar($data,'how_many_pets_owned');
		if(rgar($pets,0) != '') {
			return (int) $pets[0];
		} else {
			return 0;
		}
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
			if (count(preg_match("/".$i."\d{9}/", $petId)===1)) {
				$meta_key = "pet_{$i}_id";
				$query = new WP_User_Query( array( 'meta_key' => $meta_key, 'meta_value' => $petId ) );
				if (count($query->results) == 1) {
					$user = $query->results[0];
					break;
				}
			}
		}
		return $user;
	}
	public static function findByEmail($email) {
		$user = false;
		$query = new WP_User_Query( array( 'meta_key' => 'user_email', 'meta_value' => $email ) );
		if (count($query->results) == 1) {
			$user = $query->results[0];
		}
		return $user;
	}
}