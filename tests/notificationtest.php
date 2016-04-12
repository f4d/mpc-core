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
		$post = Notification::filterPost();
		print_r($post);
		$str = "Your message: {{ post.1 }}. ";
		$str .= 'You have {{petowner.numOfPets}} pets.';
		//$str .= ' View the petfile(s) at {{petfileUrl}}';
		$n = new Notification($owner);
		$this->log( $n->parseForm($str,$post) );
		return 1;
	}
}