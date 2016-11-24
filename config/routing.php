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

$ROUTING["/login/?"] = array(
    "handler_file" => "app/controller/LoginHandler.php",
    "handler_class" => "LoginHandler"
);


$ROUTING["/signup/?"] = array(
    "handler_file" => "app/controller/SignupHandler.php",
    "handler_class" => "SignupHandler"
);

$ROUTING["/logout/?"] = array(
    "handler_file" => "app/controller/LogoutHandler.php",
    "handler_class" => "LogoutHandler"
);

$ERRORPAGE['404'] = array(
	"handler_file" => "app/controller/404.php",
	"handler_class" => "NotFoundHandler"
);
