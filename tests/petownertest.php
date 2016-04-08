<?php
class PetOwnerTest extends Wp_Microtest {
	protected function setup() {
		$this->log("PetOwnerTest setup.");
		$this->add('parsePet');
	}
	protected function cleanup() {
		$this->log("PetOwnerTest cleanup.");
	}
	public function parsePet() {
		return true;
	}
}