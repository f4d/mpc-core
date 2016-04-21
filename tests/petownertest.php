<?php
class PetOwnerTest extends Microtest {
	protected function setup() {
		$this->log("PetOwnerTest setup.");
		$this->add('testValidOwner');
		$this->add('testInvalidOwner');
		$this->add('testValidPet');
		$this->add('testInvalidPet');
		$this->add('testValidEmail');
		$this->add('testInvalidEmail');		
		$this->add('testGetAllGuardians');		
		$this->add('testGetValidGuardians');		
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
}