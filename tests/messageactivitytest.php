<?php
class MessageActivityTest extends Microtest {
	protected function setup() {
		$this->log("MessageActivityTest setup.");
		$this->add('testGuardianResponse');
	}
	protected function cleanup() {
		//$this->scrubMeta(92,0);
		$this->log('*Cleaned meta for user #92, cyborgk@gmail.com...');
		$this->log("MessageActivityTest cleanup.");
	}
	public function testGuardianResponse() {
		$user = UserHelper::findUser('9123456789');
		$this->log(print_r($user,TRUE));
		$this->log("testing MessageActivity:GuardianResponse.");
		return false;
	}
	private function scrubMeta($userId,$numberPets) {
		$userId = $userId;
		update_user_meta( $userId, 'how_many_pets_owned', $numberPets);
		$index = $numberPets + 1;
		for($p=$index;$p<2;$p++) {
			//set info for each of the pet guardians
			update_user_meta( $userId, "pet_{$p}_name", '');
			for($g=1;$g<6;$g++) {
				$prefix = "p{$p}_guardian_{$g}_";
				$arr = array('prefix','first_name','last_name','email','mobile_phone','response');
				foreach($arr as $a) {
					//echo $prefix.$a."<br>";
					update_user_meta( $userId, $prefix.$a, '');
				}
			}
		}
	}
}