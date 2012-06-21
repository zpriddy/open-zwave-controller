<?php

require_once("config.php");

$zwaveServer = new ZwaveServer(ZWAVE_HOST, ZWAVE_PORT);
switch ($_POST["Operation"]) {
    case "Update":
        $zwaveServer->send("SETNODE~" . $_POST["Node"] . "~" . $_POST["NodeName"] . "~" . $_POST["NodeZone"]);
        echo $zwaveServer->read();        
        break;
    case "Command":
        switch ($_POST["Type"]) {
            case "Binary":
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~" . $_POST["Level"] . "~Binary Switch");
                break;

            case "Multilevel":
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~" . $_POST["Level"] . "~Multilevel Power Switch");
                break;
        }
        echo $zwaveServer->read();
        break;
}
?>
