<?php require_once("config.php") ?>
<?php MPage::BeginBlock() ?>
<h1>Devices</h1>
<?php
$zwaveServer = new ZwaveServer(ZWAVE_HOST, ZWAVE_PORT);

if (isset($_POST["btnAction"])) {
    switch ($_POST["Type"]) {
        case "Binary":
            if ($_POST["btnAction"] == "On")
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~255~Binary Switch");
            else
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~0~Binary Switch");
            break;

        case "Multilevel":
            if ($_POST["btnAction"] == "On")
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~255~Multilevel Power Switch");
            if ($_POST["btnAction"] == "Off")
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~0~Multilevel Power Switch");
            if ($_POST["btnAction"] == "Set")
                $zwaveServer->send("DEVICE~" . $_POST["Node"] . "~" . $_POST["Level"] . "~Multilevel Power Switch");
            break;
    }


    echo '<div id="response">' . $zwaveServer->read() . "</div>";
}

//get list of devices
$zwaveServer->send("ALIST");
$list = $zwaveServer->read();
$list = substr($list, 0, strlen($list)-1);
$zwaveServer->close();

$devicesList = explode("#", $list);

echo '<table id="dg">';
echo '<tr><th>Node ID</th><th>Name</th><th>Zone</th><th>&nbsp;</th></tr>';
foreach ($devicesList as $device) {
    $device = explode("~", $device);
    if ($device[1] != 1) {
        echo "<tr><td>" . $device[2] . "</td>";
        ?>
        <td>            
            <?php
            if (isset($_POST["Setup"])){
            ?>
            <form method="post" action="">
                <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>         
                <input type="hidden" name="Type" value="SetName"/>
                <input type="text" name="nodeName" value="<?php echo $device[1]; ?> "/>
                <input type="submit" name="btnAction" value="Set Name"/>
            </form>           
            <?php   }       
            else{            
                echo $device[1];
            }
            ?>            
        </td>
        <?php
        echo "<td>" . $device[3] . "</td><td>";       
        switch ($device[4]) {
            case "Binary Switch":
                ?><table>
                    <tr>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>
                                <input type="hidden" name="Type" value="Binary"/>
                                <input type="submit" name="btnAction" value="On"/>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>
                                <input type="hidden" name="Type" value="Binary"/>
                                <input type="submit" name="btnAction" value="Off"/>
                            </form>
                        </td>
                    </tr>
                </table>           
                <?php
                break;
            case ($device[4] == "Multilevel Switch" || $device[4] == "Multilevel Power Switch"):
                ?><table>
                    <tr>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>
                                <input type="hidden" name="Type" value="Multilevel"/>
                                <input type="submit" name="btnAction" value="On"/>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>
                                <input type="hidden" name="Type" value="Multilevel"/>
                                <input type="submit" name="btnAction" value="Off"/>
                            </form>
                        </td>
                        <td>                       
                            <form method="post" action="">
                                <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>
                                <input type="hidden" name="Type" value="Multilevel"/>
                                <input type="text" size="3" name="Level" value="0"/>
                                <input type="submit" name="btnAction" value="Set"/>
                            </form>
                        </td>
                    </tr>
                </table>           
                <?php
                break;
            default:
                echo $device[4];
                echo strlen($device[4]);
                break;
        }
    }
    echo "</td></tr>";
}
echo '</table>';
?>
<?php MPage::EndBlock("body") ?>
<?php MPage::Render("MDefault.php") ?> 
