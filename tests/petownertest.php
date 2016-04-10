<?php
class Wp_Microtests {
	public $html, $log, $args;
	protected $tests, $results_arr;
	public function __construct($args=[]) {
		$this->args = $args;
		$this->html = true;
		$this->results_arr = array();
		$this->tests = array();
		$this->setup();
		$this->run();
		$this->cleanup();
		$this->log_results();
	}
	public function add($method_string) {
		array_push($this->tests, $method_string);
	}
	public function log($str) {
		if ($this->html) {$this->log .= "$str<br>";}
		else $this->log .= "$str\n";
	}
	protected function setup() {
		$this->log("MicroTest parent class setup, override in child class.");
		$this->add('dummy_pass');
		$this->add('dummy_fail');
	}
	protected function cleanup() {
		$this->log("MicroTest parent class cleanup, override in child class.");
	}
	private function run() {
		$this->log("Running tests.");
		$result_str = "";
		foreach ($this->tests as $method) {
			array_push($this->results_arr,call_user_func_array(array($this, $method),array()));
		}
	}
	private function dummy_pass() {
		$this->log("Running dummy pass!");
		return true;
	}
	private function dummy_fail() {
		$this->log("Running dummy fail!");
		return false;
	}	
	private function log_results() {
		$count = 0;
		$passed = 0;
		foreach($this->results_arr as $a) {
			$count++;
			if($a===true) {$passed++;}
		}
		$this->log("Passed $passed of $count tests.");
	}
	static public function assertEqual($a,$b) {
		return ($a===$b);
	}
	static public function assertNotEqual($a,$b) {
		return ($a!==$b);
	}
}
class PetOwnerTest extends Wp_Microtests {
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
		return ($owner->user !== false);
	}
	public function testInvalidOwner() {
		$this->log("PetOwnerTest testInvalidOwner.");		
		// 9647665452 example pet owner ID
		$owner = new PetOwner('9000000000');
		print_r($owner->user);
		echo("<br>");
		return ($owner->user === false);
	}
	public function testValidPet() {
		$this->log("PetOwnerTest testValidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('1274276602');
		print_r($owner->user);
		echo("<br>");
		return ($owner->user !== false);
	}	
	public function testInvalidPet() {
		$this->log("PetOwnerTest testInvalidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('1000000000');
		print_r($owner->user);
		echo("<br>");
		return ($owner->user === false);
	}
	public function testValidEmail() {
		$this->log("PetOwnerTest testValidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('cyborgk@gmail.com');
		print_r($owner->user);
		echo("<br>");
		return ($owner->user !== false);
	}
	public function testInvalidEmail() {
		$this->log("PetOwnerTest testInvalidPet.");		
		// 1274276602 example pet ID
		$owner = new PetOwner('bookoo');
		print_r($owner->user);
		echo("<br>");
		return ($owner->user === false);
	}
}