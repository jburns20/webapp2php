<?php
/* 
 * This file is used to configure the PHP framework, MySQL database,
 * and Twig template engine.
 * 
 */

$CONFIG["debug_mode"] = true;
$CONFIG["log_requests"] = true;

$CONFIG["mysql_host"] = "localhost";
$CONFIG["mysql_username"] = "root";
$CONFIG["mysql_password"] = "root";
$CONFIG["mysql_database"] = "webapp";
$CONFIG["mysql_port"] = 8889;

$CONFIG["twig_cache"] = false;
$CONFIG["twig_cache_dir"] = "cache/twig";
