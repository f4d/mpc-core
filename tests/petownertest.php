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
	}
	protected function cleanup() {
		$this->log("PetOwnerTest cleanup.");
	}
	public function testValidOwner() {
		$this->log("PetOwnerTest testValidOwner.");		
		// 9647665452 example pet owner ID
		$owner = new PetOwner('9647665452');
		print_r($owner->user);
		echo("<br>");
		print_r($owner->pets);
		echo("<br>");
		return ($owner->user !== false);
	}
	public function testInvalidOwner() {
		$this->log("PetOwnerTest testInvalidOwner.");		
		// 9647665452 example pet owner ID
		$owner = new PetOwner('9000000000');
		print_r($owner->user);
		echo("<br>");
		print_r($owner->pets);
		echo("<br>");
		return ($owner->user === false);
	}
	public function testValidPet() {
		$this->log("PetOwnerTest testValidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('1274276602');
		print_r($owner->user);
		echo("<br>");
		print_r($owner->pets);
		echo("<br>");
		return ($owner->user !== false);
	}	
	public function testInvalidPet() {
		$this->log("PetOwnerTest testInvalidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('1000000000');
		print_r($owner->user);
		echo("<br>");
		print_r($owner->pets);
		echo("<br>");
		return ($owner->user === false);
	}
	public function testValidEmail() {
		$this->log("PetOwnerTest testValidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('cyborgk@gmail.com');
		print_r($owner->user);
		echo("<br>");
		print_r($owner->pets);
		echo("<br>");
		return ($owner->user !== false);
	}
	public function testInvalidEmail() {
		$this->log("PetOwnerTest testInvalidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('bookoo');
		print_r($owner->user);
		echo("<br>");
		print_r($owner->pets);
		echo("<br>");
		return ($owner->user === false);
	}
}