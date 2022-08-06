<?php

if ($_SERVER['SERVER_NAME'] == "www.pronet21.net" | $_SERVER['SERVER_NAME'] == "pronet21.net") {
    $user = "pronet21_root";
    $password = "pronet215231";
    $db = "ws21";
    $email_error = TRUE;
    $mostrar_error = true;
    $debug = true;
    $sistema = "/";
    //$protocolo = 'https';
} else {
    
    $user = "root";
    $password = "";
    $db = "ws21";
    
}
date_default_timezone_set("America/La_Paz");
define("HOST", "localhost");
define("USER", $user);
define("PASSWORD", $password);
define("DB", $db);
define("EMAIL_ERROR",'ynfantes@gmail.com');