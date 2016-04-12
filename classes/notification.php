<?php
class Notification{
	public $petOwner;
	public function __construct($petOwner) {
		$this->petOwner = $petOwner;
	}
	public function parseForm($str,$post) {
		$loader = new Twig_Loader_Array(array('index' => $str));
		$twig = new Twig_Environment($loader);
		return $twig->render('index', array('post'=>$post,'petowner' => $this->petOwner));				
	}
	static public function filterPost() {
		$post = [];
		foreach ($_POST as $key => $value) {
			if (preg_match("/input_/",$key)===1) {
				$key = str_replace("_",".",str_replace("input_","",$key));
				$post[$key] = $value;
			}
		}
		return $post;
	}
}