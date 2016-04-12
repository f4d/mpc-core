<?php
class NotificationTest extends Microtest {
	protected function setup() {
		$this->log("NotificationTest setup.");
		$this->add('parseForm');
	}
	protected function cleanup() {
		$this->log("NotificationTest cleanup.");
	}
	public function parseForm() {
		$owner = new PetOwner('cyborgk@gmail.com');
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$str = $n->parseForm("Your message: {{ post.1 }}. ",$post);
		$str2 = $n->parseForm('You have {{petowner.numOfPets}} pets.',$post);
		//$str .= ' View the petfile(s) at {{petfileUrl}}';
		$this->log( $str.$str2 );
		return ($str2 == 'You have 3 pets.');
	}
	public function parseGuardianData() {

	}
}