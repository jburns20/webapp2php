<?php

class Request {
	public $path;
	public $useragent;
	public $method;
	public $referer;
	public $request_params;
	public $path_params;
	public $file_params;
	public $cookies;
	
	public function get($param_name) {
		if (array_key_exists($param_name, $this->request_params)) {
			return $this->request_params[$param_name];
		} else {
			return NULL;
		}
	}
}