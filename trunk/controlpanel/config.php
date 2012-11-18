<?php

session_start();
error_reporting(E_ALL);

//z-wave server settings
define("ZWAVE_HOST", "127.0.0.1");
define("ZWAVE_PORT", 6004);

//Database settings
define("HOST", "127.0.0.1");
define("DATABASE", "zwave");
define("USER", "root");
define("PASSWORD", "psx1242");

//Cookie expire after a month
define("EXPIRE", time() + 60 * 60 * 24 * 30);

//Page settings
define("TITLE", "Lights Control");

define("URI", $_SERVER["SERVER_NAME"]);

//skip login if chosen to keep signed in
if (isset($_COOKIE["SignedIn"])) {
    $_SESSION["UserId"] = $_COOKIE["UserId"];
    $_SESSION["Username"] = $_COOKIE["Username"];
}

//redrirect to login if user is not logged in
if (!isset($_SESSION["Username"])) {
    if (substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1) != "login.php")
        header("location:login.php");
}

function __autoload($classname) {
    require_once("lib/" . $classname . ".php");
}

?>
