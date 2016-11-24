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

$begintime = microtime(true);
require_once "vendor/autoload.php";
require_once "framework/ALL.php";

use RedBeanPHP\R;

global $CONFIG;
$CONFIG = array();
require_once("config/config.php");
$debug = $CONFIG['debug_mode'];
R::setup('mysql:host=' . $CONFIG['mysql_host'] . ';dbname=' . $CONFIG['mysql_database'], $CONFIG['mysql_username'], $CONFIG['mysql_password']);

$request = new Request();
$request->path = "";
if (isset($_SERVER['PATH_INFO'])) {
$request->path = $_SERVER['PATH_INFO'];
}
if ($request->path == null || $request->path == "") {
	$request->path = $_SERVER['ORIG_PATH_INFO'];
}
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
if ($CONFIG["log_requests"]) error_log($request->method . " " . $request->path);

$ROUTING = array();
$ERRORPAGE = array();
require_once("config/routing.php");

function handle_with_rule($request, $rule) {
    global $CONFIG;
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
        if (!file_exists($rule['handler_file'])) {
            throw new Exception("webapp2php ERROR: The handler file \"" . $rule['handler_file'] . "\" does not exist.");
        }
        require_once $rule['handler_file'];
        if (!class_exists($rule['handler_class'])) {
            throw new Exception("webapp2php ERROR: The handler class \"" . $rule['handler_class'] . "\" does not exist.");
        }
        $instance = new $rule['handler_class']($request, $rule);
        if ($request->method == "POST") {
            $instance->post();
        } else {
            $instance->get();
        }
        $response = $instance->response;
        if ($response->response_code != null) {
            header($response->response_code);
        } else {
            $response->response_code = "HTTP/1.1 200";
        }
        foreach($response->cookies as $cookie) {
            setcookie($cookie['name'],
                      $cookie['value'],
                      $cookie['expire'],
                      $cookie['path'],
                      $cookie['domain'],
                      $cookie['secure'],
                      $cookie['httponly']);
        }
        foreach($response->headers as $header) {
            header($header['key'] . ": " . $header['value']);
            if ($header['key'] == "Location") {
                $response->response_code = "HTTP/1.1 302";
            }
        }
        if ($CONFIG["log_requests"]) error_log($response->response_code);
        echo $response->contents;
    }
}

$match_found = FALSE;
foreach ($ROUTING as $pattern => $rule) {
	$pattern = "%^" . $pattern . "$%";
	preg_match($pattern, $request->path, $matches);
	
	if (count($matches) > 0 && $matches[0] == $request->path) {
		$match_found = TRUE;
		array_shift($matches);
		$request->path_params = $matches;
		handle_with_rule($request, $rule);
		break;
	}
}
if (!$match_found) {
	$rule = $ERRORPAGE[404];
	handle_with_rule($request, $rule);
}
R::close();

$endtime = microtime(true);
$totaltime = ($endtime - $begintime)*1000;

if ($debug) {
	echo "\n<hr>\n";
	echo "request took " . intval($totaltime)/1000 . " seconds.<br>\n";
	echo "<table class='debug'><tbody>";
	echo "<tr><td>path</td><td>" . ($request->path) . "</td></tr>\n";
	echo "<tr><td>method</td><td>" . ($request->method) . "</td></tr>\n";
	echo "<tr><td>useragent</td><td>" . ($request->useragent) . "</td></tr>\n";
	echo "<tr><td>referer</td><td>" . ($request->referer) . "</td></tr>\n";
	echo "<tr><td>request params</td>\n<td>";
	echo var_export($request->request_params, true);
	echo "</tr>\n<tr><td>path params</td>\n<td>";
	echo var_export($request->path_params, true);
	echo "</tr>\n<tr><td>file params</td>\n<td>";
	echo var_export($request->file_params, true);
	echo "</tr>\n<tr><td>request cookies</td>\n<td>";
	echo var_export($request->cookies, true);
	echo "</tr>\n<tr><td>response cookies</td>\n<td>";
	echo var_export($response->cookies, true);
	echo "</td></tr></tbody></table>";
	echo "<style>.debug td {border: 1px solid #CCC; padding: 2px;} .debug {border-collapse: collapse;}</style>";
}
