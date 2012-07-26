<?php

class Handler {
	public $request;
	public $response;
	private $twig;
	
	function __construct($request) {
		$this->request = $request;
		$this->response = new Response();
		Twig_Autoloader::register();
		$loader_file = new Twig_Loader_Filesystem('src/templates');
		$loader_str = new Twig_Loader_String();
		$config = json_decode(file_get_contents("config/config.json"), TRUE);
		$twig_setup  = $config['twig'];
		if ($twig_setup['cache_on']) {
			$this->twig_file = new Twig_Environment($loader_file, array('cache' => $twig_setup['cache_dir']));
			$this->twig_str = new Twig_Environment($loader_str, array('cache' => $twig_setup['cache_dir']));
		} else {
			$this->twig_file = new Twig_Environment($loader_file, array());
			$this->twig_str = new Twig_Environment($loader_str, array());
		}
	}
	public function get($request) {
		$this->response->error(405);
		$this->write("<h1>Error 405: Method \"GET\" not allowed.</h1>");
	}
	public function post($request) {
		$this->response->error(405);
		$this->write("<h1>Error 405: Method \"POST\" not allowed.</h1>");
	}
	public function write($text) {
		$this->response->write($text);
	}
	public function write_file($filename) {
		$this->render_write_file($filename, array());
	}
	public function render($text, $params) {
		//$params['user'] = $this->user;
		return $this->twig_str->render($text, $params);
	}
	public function render_file($filename, $params) {
		//$params['user'] = $this->user;
		return $this->twig_file->render($filename, $params);
	}
	public function render_write($text, $params) {
		$output = $this->render($text, $params);
		$this->write($output);
	}
	public function render_write_file($filename, $params) {
		$output = $this->render_file($filename, $params);
		$this->write($output);
	}
}