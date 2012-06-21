<?php

require_once("config.php");
if (isset($_POST["Operation"])) {
    $dataobject = new Dataclass();

    switch ($_POST["Operation"]) {
        case "Add":
            $result = $dataobject->insert_room($_POST["floor"], $_POST["roomName"]);
            if ($result)
                echo $_POST["roomName"] . " was added";
            else
                echo $_POST["roomName"] . " was not added";
            break;

        case "Update":
            if (isset($_POST["roomid"])) {
                $result = $dataobject->update_room($_POST["roomid"], $_POST["floor"], $_POST["roomName"]);
                if ($result)
                    echo $_POST["roomName"] . " was updated";
                else
                    echo $_POST["roomName"] . " was not updated";
            }
            break;

        case "Delete":
            if (isset($_POST["roomid"])) {
                $result = $dataobject->delete_room($_POST["roomid"]);
                if ($result)
                    echo $_POST["roomName"] . " was deleted";
                else
                    echo $_POST["roomName"] . " was not deleted";
            }
            break;
    }
}
else
    echo "nothing to execute";
?>