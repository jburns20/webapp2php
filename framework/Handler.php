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

class Handler {
	public $request;
	public $response;
	private $twig;
	
	function __construct($request) {
		$this->request = $request;
		$this->response = new Response();
		Twig_Autoloader::register();
		$loader_file = new Twig_Loader_Filesystem('app/templates');
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