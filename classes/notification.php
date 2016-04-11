<?php
class Notification{
	$petOwner;
	public function __construct($petOwner) {
		$this->petOwner = $petOwner;
	}
	public function parsePetfile($str,$url) {
		$loader = new Twig_Loader_Array(array('index' => $str));
		$twig = new Twig_Environment($loader);
		return $twig->render('index', array('petfileUrl' => $url));				
	}
}