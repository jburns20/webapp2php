<?php
class Response {
	public $headers;
	public $cookies;
	public $contents;
	public $response_code;
	
	function __construct() {
		$this->headers = array();
		$this->cookies = array();
		$this->error = null;
	}
	
	public function write($text) {
		$this->contents .= $text;
	}
	
	public function set_cookie($name, $value, $expire=0, $path=NULL, $domain=NULL, $secure=FALSE, $httponly=FALSE) {
		$cookie = array('name'=>$name,
						'value'=>$value,
						'expire'=>$expire,
						'path'=>$path,
						'domain'=>$domain,
						'secure'=>$secure,
						'httponly'=>$httponly);
		$this->cookies[] = $cookie;
	}
	
	public function add_header($key, $value) {
		$header = array('key'=>$key, 'value'=>$value);
		$this->headers[] = $header;
	}
	
	public function redirect($location) {
		$this->add_header("Location", $location);
	}
	
	public function error($number) {
		$this->response_code = "HTTP/1.0 " . $number;
	}
}