<?php

/**
 * Petfile is a utility class that represents pet and guardian data.
 * It abstract the details of pets and guardian, making it easy to loop
 * through and test all the guardians. It also provides utility functions
 * for taking gravity form data, and converting it to use the same form
 * as the user meta data, making it easy to compare the two. 
 */

class Petfile {
	public $guardians, $petfile;
	public function __construct( $petfile ) {
		$this->petfile = $petfile;
	}
	/**
	 * setGuardian is used when to store minimal data about the guardian with
	 * the Petfile object, in this case just the email.
	 * @param integer $guardianNum   Which guardian is it?
	 * @param string $email          The guardian's email
	 */
	public function setGuardian($guardianNum,$email) {
		$this->guardians[$guardianNum] = new Guardian2($email);
	}
	public function howManyGuardians() {
		return count($this->guardians);
	}
	/**
	 * post2Data converts data from the Add Pets & Guardians form
	 * and changes it into a form where it can be compared with the user
	 * meta data. 
	 * @param  $post array   Array containing post data, normally $_POST
	 * @return array         Returned array has keys named to match user meta
	 */
	static public function addPetsPost2Data($post) {
		$data = [];
		$data['how_many_pets_owned'] = (int) rgar($post,'input_59');
		$data['num_guardians_p1'] = $post["input_114"];
		$data['num_guardians_p2'] = $post["input_220"];
		$data['num_guardians_p3'] = $post["input_223"];
		$data['num_guardians_p4'] = $post["input_219"];
		$data['num_guardians_p5'] = $post["input_221"];
		$formFields = array(
			'p1_guardian_1_email'=>'112','p1_guardian_2_email'=>'118',
			'p1_guardian_3_email'=>'122','p1_guardian_4_email'=>'126',
			'p1_guardian_5_email'=>'131','p2_guardian_1_email'=>'135',
			'p2_guardian_2_email'=>'137','p2_guardian_3_email'=>'140',
			'p2_guardian_4_email'=>'143','p2_guardian_5_email'=>'146',
			'p3_guardian_1_email'=>'151','p3_guardian_2_email'=>'154',
			'p3_guardian_3_email'=>'157','p3_guardian_4_email'=>'160',
			'p3_guardian_5_email'=>'163','p4_guardian_1_email'=>'167',
			'p4_guardian_2_email'=>'170','p4_guardian_3_email'=>'173',
			'p4_guardian_4_email'=>'176','p4_guardian_5_email'=>'179',
			'p5_guardian_1_email'=>'183','p5_guardian_2_email'=>'186',
			'p5_guardian_3_email'=>'189','p5_guardian_4_email'=>'192',
			'p5_guardian_5_email'=>'195'
		);
		foreach($formFields as $key => $id) {
			$data[$key] = $post["input_$id"];
		}
		return $data;
	}
	/**
	 * petfilePost2Data lets you take form data from the update petfile
	 * and change it into a form where it can be compared with the user
	 * meta data. If you need to block notifications from some other form,
	 * you will want to right a function like this, only rewrite to use 
	 * the correct gravity form ID numbers in the $formFields array
	 * @param  string $petNum    Which petfile (1-5) is being updated
	 * @param  array $post       Form data, usually $_POST unless unit testing
	 * @return array             Array with keys that match user meta keys
	 */
	static public function petfilePost2Data($petNum,$post) {
		$formFields = array(
			"p{$petNum}_guardian_1_email"=>'167',
			"p{$petNum}_guardian_2_email"=>'173',
			"p{$petNum}_guardian_3_email"=>'177',
			"p{$petNum}_guardian_4_email"=>'182',
			"p{$petNum}_guardian_5_email"=>'185'
		);
		foreach($formFields as $key => $id) {
			$data[$key] = $post["input_$id"];
		}
		return $data;
	}
	/**
	 * getPet is used to model pets and guardians in the submitted form data
	 * The objects created this way will be used in checkGuardianNotifications
	 * and compared against objects created from the user meta data, to check
	 * if guardians are new based on email. 
	 * @param  string $petNum   which pet (1-5)
	 * @param  array $data      user meta data, or array with same format
	 * @return Petfile object      returns a Petfile object with guardian info set
	 */
	static public function getPet($petNum,$data) {
		$pet = new Petfile($petNum);
		$num = (int) $data["num_guardians_p{$petNum}"];
		//set info for each of the pet guardians
		for($g=1;$g<$num+1;$g++) {
			//p1_guardian_1_email
			$f = "p{$petNum}_guardian_{$g}_email";
			$pet->setGuardian($g,$data[$f]);
		} 
		return $pet;
	}



}