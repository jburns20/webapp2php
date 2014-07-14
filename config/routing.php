<?php
/* 
 * This file is used to configure which handler class is invoked when the user
 * visits a given URL.
 * 
 */

$ROUTING["/?"] = array(
	"handler_file" => "app/controller/HomeHandler.php",
	"handler_class" => "HomeHandler"
);

$ERRORPAGE['404'] = array(
	"handler_file" => "app/controller/404.php",
	"handler_class" => "NotFoundHandler"
);