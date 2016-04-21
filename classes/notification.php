<?php
class Notification{
	public $petOwner;
	public function __construct($petOwner) {
		$this->petOwner = $petOwner;
	}
	public function parseGuardianTemplate($str,$post,$guardian,$from='') {
		$loader = new Twig_Loader_Array(array('index' => $str));
		$twig = new Twig_Environment($loader);
		return $twig->render('index', array(
			'post'=>$post,
			'petowner' => $this->petOwner,
			'guardian' => $guardian,
			'from'=>$from));	
	}	
	public function parseOwnerTemplate($str,$post,$from='') {
		$loader = new Twig_Loader_Array(array('index' => $str));
		$twig = new Twig_Environment($loader);
		return $twig->render('index', array(
			'post'=>$post,
			'petowner' => $this->petOwner,
			'from'=>$from));
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
	static public function getTemplate($post_id) {
		return esc_attr(get_post_meta( $post_id, '_notification_text', true ));
	}
}