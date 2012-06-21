<?php require_once("config.php");
    unset($_SESSION["Username"]);
    setcookie('SignedIn','',time()-3600);
    header("location:login.php");
?>