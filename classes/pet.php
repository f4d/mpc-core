<?php
class Pet {
	const PET_ID_FIELD = '147';
	const PF1_ID = '6';
	const PF2_ID = '57';
	const PF3_ID = '58';
	const PF4_ID = '59';
	const PF5_ID = '60';
	public $petOwnerId, $petNum, $msg, $name, $numOfGuardians;
	public function __construct( $petNum, $petOwnerId, $data ) {
		$this->data = $data;
		$this->petOwnerId = $petOwnerId;
		$this->petNum = $petNum;
		$this->numOfGuardians = rgar(rgar($data,"p{$petNum}_how_many_guardians"),0);;
		$this->name = rgar(rgar($data,"pet_{$petNum}_name"),0);
		$this->type = rgar(rgar($data,"pet_{$petNum}_type"),0);
		$this->petId = rgar(rgar($data,"pet_{$petNum}_id"),0);
		$this->setGuardians();
	}
	public function findPetfileUrl() {
		$petfileArr = array('1'=>Pet::PF1_ID,'2'=>Pet::PF2_ID,'3'=>Pet::PF3_ID,
			'4'=>Pet::PF4_ID,'5'=>Pet::PF5_ID);
		//use $this->petNum & $this->petOwnerId, lookup in petfile{n} gravityform
		$search_criteria = array();
		$search_criteria['field_filters'][] = array( 
			'key' => Pet::PET_ID_FIELD, 
			'value' => $this->petId );
		$entries = GFAPI::get_entries( $petfileArr[$this->petNum], $search_criteria );
		$last = array_shift($entries);
		$callbackUrl = TwilioHelper::prepUrl('/guardian-access-petfile-1/?eid='.$last['id']); 
		return $callbackUrl;
	}
	private function setGuardians() {
		for($i=1;$i<($this->numOfGuardians+1);$i++) {
			$prefix = "p{$this->petNum}_guardian_{$i}_";
			$arr = array('prefix','first_name','last_name','email','mobile_phone','response');
			$hash = array();
			foreach($arr as $a) {
				$tempArr = rgar($this->data,$prefix.$a);
				$hash[$a] = rgar($tempArr,0);
			}
			$this->setGuardian($i,$hash);
		}
	}
	private function setGuardian($guardianNum,$data) {
		$this->guardians[$guardianNum] = new Guardian($data,$this->petId,$this->petNum,
			$guardianNum,$this->name,$this->type,$this->findPetfileUrl());
	}

	
}
