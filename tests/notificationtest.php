<?php
class NotificationTest extends Microtest {
	protected function setup() {
		$this->log("NotificationTest setup.");
		$this->add('parseOwnerTemplate');
		$this->add('parseGuardianTemplate');
	}
	protected function cleanup() {
		$this->log("NotificationTest cleanup.");
	}
	public function parseOwnerTemplate() {
		$this->log("parseOwnerTemplate test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$str = $n->parseOwnerTemplate("Your message: {{ post.1 }}. ",$post);
		$str2 = $n->parseOwnerTemplate('You have {{petowner.numOfPets}} pets.',$post);
		//$str .= ' View the petfile(s) at {{petfileUrl}}';
		$this->log( $str.$str2 );
		return ($str2 == 'You have 3 pets.');
	}
	public function parseGuardianTemplate() {
		$this->log("parseGuardianTemplate test.");
		$owner = new PetOwner('cyborgk@gmail.com');
		$guardian = $owner->pets[1]->guardians[1];		
		$n = new Notification($owner);
		$post = Notification::filterPost();
		$template = "{{guardian.first_name}} {{guardian.last_name}} View the petfile(s) at {{guardian.petfileUrl}}";
		$str = $n->parseGuardianTemplate($template,$post,$guardian);
		$this->log( $str );
		$expected = 'Draven Powers View the petfile(s) at http://petguardian.staging.wpengine.com/guardian-access-petfile-1/?eid=';
		return ($str == $expected);
	}
}