<?php
class PetOwnerTest extends Microtest {
	protected function setup() {
		$this->log("PetOwnerTest setup.");
		$this->add('testValidOwner');
		$this->add('testInvalidOwner');
		$this->add('testValidPet');
		$this->add('testInvalidPet');
		$this->add('testForValidPetIds');
		$this->add('testValidEmail');
		$this->add('testInvalidEmail');		
		$this->add('testGetAllGuardians');		
		$this->add('testGetValidGuardians');		
		$this->add('testGetValidGuardiansForPet');		
	}
	protected function cleanup() {
		$this->log("PetOwnerTest cleanup.");
	}
	public function testValidOwner() {
		$this->log("PetOwnerTest testValidOwnerId.");		
		// 9647665452 example pet owner ID
		$owner = new PetOwner('9647665452');
		$this->log("Number of pets: ".$owner->numOfPets);
		return ($owner->user !== false);
	}
	public function testInvalidOwner() {
		$this->log("PetOwnerTest testInvalidOwnerId.");		
		// 9647665452 example pet owner ID
		$owner = new PetOwner('9000000000');
		$this->log("Number of pets: ".$owner->numOfPets);
		return ($owner->user === false);
	}
	public function testValidPet() {
		$this->log("PetOwnerTest testValidPetId.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('1274276602');
		$this->log("Number of pets: ".$owner->numOfPets);
		return ($owner->user !== false);
	}
	public function testInvalidPet() {
		$this->log("PetOwnerTest testInvalidPetId.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('1000000000');
		$this->log("Number of pets: ".$owner->numOfPets);
		return ($owner->user === false);
	}
	public function testForValidPetIds() {
		$this->log("PetOwnerTest testForValidPetIds.");		
		$id1 = "0000000000";
		$id2 = "9876543210";
		$id3 = "1223456123";
		$id4 = "5000000000";
		$test1 = PetOwner::isValidPetId($id1);
		$test2 = PetOwner::isValidPetId($id2);
		$test3 = PetOwner::isValidPetId($id3);
		$test4 = PetOwner::isValidPetId($id4);
		$result = ($test1 === false && $test2 ===false && $test3===true && $test4===true);
		$this->log("TestForValidPetIds result:".$result);
		return $result;
	}
	public function testValidEmail() {
		//cyborgk@gmail.com pet owner ID 9342390942
		$this->log("PetOwnerTest testValidOwnerEmail.");		
		$owner = new PetOwner('cyborgk@gmail.com');
		$this->log("Number of pets: ".$owner->numOfPets);
		return ($owner->user !== false);
	}
	public function testInvalidEmail() {
		$this->log("PetOwnerTest testInvalidOwnerEmail.");		
		$owner = new PetOwner('bookoo');
		$this->log("Number of pets: ".$owner->numOfPets);
		return ($owner->user === false);
	}
	public function testGetAllGuardians() {
		$this->log("PetOwnerTest testGetAllGuardians.");		
		$owner = new PetOwner('cyborgk@gmail.com');
		$g = $owner->getAllGuardians();
		$this->log("Number of guardians for cyborgk@gmail.com: ".count($g));
		return (count($g) === 5);
	}	
	public function testGetValidGuardians() {
		//user id 92
		$this->log("PetOwnerTest testGetAllGuardians.");		
		$owner = new PetOwner('cyborgk@gmail.com');
		$g = $owner->getValidGuardians();
		$this->log("Number of valid guardians for cyborgk@gmail.com: ".count($g));
		return (count($g) === 5);
	}
	public function testGetValidGuardiansForPet() {
		//user id 92
		$this->log("PetOwnerTest testGetValidGuardiansForPet.");		
		$owner = new PetOwner('cyborgk@gmail.com');
		$g = $owner->getValidPetGuardians(1);
		print_r($g);
		$this->log("Number of valid guardians for pet 1, cyborgk@gmail.com: ".count($g));
		return (count($g) === 3);
	}	
}