<?php
require_once("config.php");

if (isset($_POST["txtPassword"])) {
    $dataobject = new Dataclass();
    $dataobject->update_password($_POST["txtPassword"]);
    echo "Password has been set!";
}
?>