<?php
# Copyright (c) 2012, jburns20
# All rights reserved.
# 
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#     * Redistributions of source code must retain the above copyright
#       notice, this list of conditions and the following disclaimer.
#     * Redistributions in binary form must reproduce the above copyright
#       notice, this list of conditions and the following disclaimer in the
#       documentation and/or other materials provided with the distribution.
#     * Neither the name of jburns20 nor the
#       names of its contributors may be used to endorse or promote products
#       derived from this software without specific prior written permission.
# 
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
# ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL JBURNS20 BE LIABLE FOR ANY
# DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
# (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
# ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

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
		$this->response_code = "HTTP/1.1 " . $number;
	}
	
	public function reset() {
		$this->contents = "";
		$this->headers = array();
		$this->cookies = array();
		$this->error = null;
		$this->response_code = null;
	}
}