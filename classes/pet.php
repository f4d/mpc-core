<?php
class Pet {
	const PET_OWNER_FIELD = '204';
	const PF1_ID = '6';
	const PF2_ID = '57';
	const PF3_ID = '58';
	const PF4_ID = '59';
	const PF5_ID = '60';
	public $petOwnerId, $petfile, $msg, $name;
	public function __construct( $petfile, $petOwnerId, $data ) {
		$this->petOwnerId = $petOwnerId;
		$this->petfile = $petfile;
		$this->name = rgar(rgar($data,"pet_{$petfile}_name"),0);
		$this->guardians = array();
		$this->data = $data;
	}
	public function setGuardian($guardianNum,$data) {
		$this->guardians[$guardianNum] = new Guardian($data);
	}
	public function findPetfileUrl() {
		$petfileArr = array('1'=>Pet::PF1_ID,'2'=>Pet::PF2_ID,'3'=>Pet::PF3_ID,
			'4'=>Pet::PF4_ID,'5'=>Pet::PF5_ID);
		//use $this->petfile & $this->petOwnerId, lookup in petfile{n} gravityform
		$search_criteria = array();
		$search_criteria['field_filters'][] = array( 
			'key' => Pet::PET_OWNER_FIELD, 
			'value' => $this->petOwnerId );
		$entries = GFAPI::get_entries( $petfileArr[$this->petfile], $search_criteria );
		$last = array_shift($entries);
		$callbackUrl = TwilioHelper::prepUrl('/guardian-access-petfile-1/?eid='.$last['id']); 
		return $callbackUrl;
	}

	static public function getPet($petOwnerId,$petNum,$data) {
		$pet = new Pet($petNum,$petOwnerId,$data);
		//set info for each of the pet guardians
		for($i=1;$i<6;$i++) {
			$prefix = "p{$petNum}_guardian_{$i}_";
			$arr = array('prefix','first_name','last_name','email','mobile_phone','response');
			$hash = array();
			foreach($arr as $a) {
				$tempArr = rgar($data,$prefix.$a);
				$hash[$a] = rgar($tempArr,0);
			}
			$pet->setGuardian($i,$hash);
		}
		return $pet;
	}
}
