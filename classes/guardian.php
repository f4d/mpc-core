<?php
class Guardian {
	public $prefix, $first_name, $last_name, $email, $mobile_phone, $response;
	public function __construct( $meta, $petId, $petNum, $guardNum, $petname, $pettype, $petfileUrl = '' ) {
		$this->petId = $petId;
		$this->petNum = $petNum;
		$this->guardNum = $guardNum;
		$this->petname = $petname;
		$this->pettype = $pettype;
		$this->petfileUrl = $petfileUrl;
		$this->mobile_phone = rgar( $meta, 'mobile_phone' );
		$this->response = rgar( $meta, 'response' );
		$this->prefix = rgar( $meta, 'prefix' );
		$this->first_name = rgar( $meta, 'first_name' );
		$this->last_name = rgar( $meta, 'last_name' );
		$this->email = rgar( $meta, 'email' );
	}
}