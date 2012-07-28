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

require_once "framework/ALL.php";

$begintime = microtime(true);

$routing_table = json_decode(file_get_contents("config/routing.json"), TRUE);
if ($routing_table == NULL) {
	error_log("ERROR: The routing table is misconfigured.");
	die();
}
$request = new Request();
$request->path = $_SERVER['PATH_INFO'];
$request->useragent = $_SERVER['HTTP_USER_AGENT'];
$request->method = $_SERVER['REQUEST_METHOD'];
if (array_key_exists("HTTP_REFERER", $_SERVER)) {
	$request->referer = $_SERVER['HTTP_REFERER'];
} else {
	$request->referer = "";
}
$params = array_merge($_GET, $_POST);
$request->cookies = $_COOKIE;
$request->request_params = $params;
$request->file_params = $_FILES;
error_log($request->method . " " . $request->path);
$config = json_decode(file_get_contents("config/config.json"), TRUE);
if ($config == NULL) {
	error_log("ERROR: The config table is misconfigured.");
	die();
}
$debug = $config['debug_mode'];
$mysql_setup  = $config['mysql'];
R::setup('mysql:host=' . $mysql_setup['host'] . ';dbname=' . $mysql_setup['database'], $mysql_setup['username'], $mysql_setup['password']);

$match_found = FALSE;
foreach ($routing_table['routing'] as $rule) {
	$pattern = "%^" . $rule['pattern'] . "$%";
	preg_match($pattern, ($request->path), $matches);
	
	if (count($matches) > 0 && $matches[0] == $request->path) {
		$match_found = TRUE;
		array_shift($matches);
		$request->path_params = $matches;
		if (array_key_exists('static_file', $rule)) {
			$contents = file_get_contents($rule['static_file']);
			if (array_key_exists('static_type', $rule)) {
				$type = $rule['static_type'];
			} else {
				$type = mime_content_type($rule['static_file']);
			}
			header("Content-Type: " . $type);
			echo $contents;
		} else {
			require_once $rule['handler_file'];
			$instance = new $rule['handler_class']($request);
			if ($request->method == "POST") {
				$instance->post();
			} else {
				$instance->get();
			}
			$response = $instance->response;
			if ($response->response_code != null) {
				header($response->response_code);
			} else {
				$response->response_code = "HTTP/1.0 200";
			}
			foreach(($response->cookies) as $cookie) {
				setcookie($cookie['name'],
						  $cookie['value'],
						  $cookie['expire'],
						  $cookie['path'],
						  $cookie['domain'],
						  $cookie['secure'],
						  $cookie['httponly']);
			}
			foreach(($response->headers) as $header) {
				header($header['key'] . ": " . $header['value']);
				if ($header['key'] == "Location") {
					$response->response_code = "HTTP/1.0 302";
				}
			}
			error_log($response->response_code);
			echo $response->contents;
		}
		break;
	}
}
if (!$match_found) {
	header("HTTP/1.0 404");
	error_log("HTTP/1.0 404");
	echo "<h1>Error 404: File Not Found.</h1>";
}
R::close();

$endtime = microtime(true);
$totaltime = ($endtime - $begintime)*1000;

if ($debug) {
	echo "\n<hr>\n";
	echo "request took " . $totaltime . " milliseconds.<br>\n";
	echo "path: " . ($request->path) . "<br>\n";
	echo "method: " . ($request->method) . "<br>\n";
	echo "useragent: " . ($request->useragent) . "<br>\n";
	echo "referer: " . ($request->referer) . "<br>\n";
	echo "request params: <br>\n";
	$param_string = var_dump($request->request_params);
	echo "<br>\npath params: <br>\n";
	$param_string = var_dump($request->path_params);
	echo $param_string;
}

