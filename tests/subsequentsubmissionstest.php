<?php
class SubsequentSubmissionsTest extends Microtest {
	protected function setup() {
		$this->log("MessageActivityTest setup.");
		$this->add('testPetOwnerId');
		$this->add('testNumberOfPets');
		$this->add('testIsPetNew');
	}
	protected function cleanup() {
		//$this->scrubMeta(92,0);
		//$this->log('*Cleaned meta for user #92, cyborgk@gmail.com...');
		$this->log("MessageActivityTest cleanup.");
	}
	public function testPetOwnerId() {
		$this->log("running testPetOwnerId");
		$user = UserHelper::findUser('9123456789');
		$meta = get_metadata('user', $user->ID);
		$pet_owner_id = SubsequentSubmissions::petOwnerId($meta);
		return ($pet_owner_id=='9123456789');
	}
	public function testNumberOfPets() {
		$this->log("running testNumberOfPets");
		$user = UserHelper::findUser('9123456789');
		$meta = get_metadata('user', $user->ID);
		$numPets =  SubsequentSubmissions::numberOfPets($meta);
		print_r($numPets);
		
		$this->log("Number of pets $numPets ");
		return ($numPets=='3');
	}
	public function testIsPetNew() {
		$this->log("running testIsPetNew");
		$user = UserHelper::findUser('9123456789');
		$meta = get_metadata('user', $user->ID);		
		$new = SubsequentSubmissions::isPetNew('4', $meta);
		$this->log("New pet? $new");
		return ($new===TRUE);
	}
	public function testPetfileGetPet() {
		$this->log("running testIsPetNew");
		$user = UserHelper::findUser('9123456789');
		$meta = get_metadata('user', $user->ID);		
	}
}