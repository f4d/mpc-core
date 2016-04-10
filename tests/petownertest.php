<?php
class PetOwnerTest extends Wp_Microtest {
	protected function setup() {
		$this->log("PetOwnerTest setup.");
		$this->add('test1');
	}
	protected function cleanup() {
		$this->log("PetOwnerTest cleanup.");
	}
	public function test1() {
		$this->log("PetOwnerTest test1.");		
		// 9647665452 example pet owner ID
		// 1274276602 example pet ID

		return true;
	}
}