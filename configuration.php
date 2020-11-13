<?php

define('SERVER_DATABASE', 'localhost');
define('USERNAME_DATABASE', 'admin');
define('PASSWORD_DATABASE', 'password123@&Security');
define('NAME_DATABASE', 'login');

$link = mysql_connection(SERVER_DATABASE, USERNAME_DATABASE, PASSWORD_DATABASE, NAME_DATABASE);

if($link === false)
{
    die("Error: Couldn't connect. " . mysql_connection_error());
}
?>