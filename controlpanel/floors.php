<?php

require_once("config.php");
if (isset($_POST["Operation"])) {
    $dataobject = new Dataclass();
    switch ($_POST["Operation"]) {
        case "Add":
            $result = $dataobject->insert_Floor($_POST["FloorName"]);
            if ($result)
                echo $_POST["FloorName"] . " was added";
            else
                echo $_POST["FloorName"] . " was not added";
            break;

        case "Update":
            if (isset($_POST["floorid"])) {
                $result = $dataobject->update_Floor($_POST["floorid"], $_POST["FloorName"]);
                if ($result)
                    echo $_POST["FloorName"] . " was updated";
                else
                    echo $_POST["FloorName"] . " was not updated";
            }
            break;

        case "Delete":
            if (isset($_POST["floorid"])) {
                $result = $dataobject->delete_Floor($_POST["floorid"]);
                if ($result)
                    echo $_POST["FloorName"] . " was deleted";
                else
                    echo $_POST["FloorName"] . " was not deleted";
            }
            break;
    }
}
else
    echo "nothing to execute";
?>