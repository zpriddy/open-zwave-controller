<?php require_once("config.php") ?>
<?php MPage::BeginBlock() ?>
<?php
//a very primitive way to get the devices status.  This does not update when the devices change state.
//$log = file_get_contents("zwaveController/OZW_Log.txt");
//$states = array();

//function backwardStrpos($haystack, $needle, $offset = 0) {
//    $length = strlen($haystack);
//    $offset = ($offset > 0) ? ($length - $offset) : abs($offset);
//    $pos = strpos(strrev($haystack), strrev($needle), $offset);
//    return ($pos === false) ? false : ( $length - $pos - strlen($needle) );
//}
//
//for ($node = 1; $node <= 13; $node++) {
//    $status = 0;
//    if (strpos($log, "report from node $node: level=")) {
//        if ($node < 10)
//            $status = substr($log, strpos($log, "report from node $node: level=") + 26, 3);
//        else
//            $status = substr($log, strpos($log, "report from node $node: level=") + 27, 3);
//    }
//    if (strpos($status, "2"))
//        $status = 0;
//    $states[$node] = $status;
//}

//for ($node = 1; $node <= 13; $node++) {
//    if (backwardStrpos($log, "Setting node $node to level")) {
//        if ($node < 10) {
//            $status = substr($log, backwardStrpos($log, "Setting node $node to level") + 24, 3);
//            if (strpos($status, "2"))
//                $status = 0;
//        } else {
//            $status = substr($log, backwardStrpos($log, "Setting node $node to level") + 25, 3);
//            if (strpos($status, "2"))
//                $status = 0;
//        }
//    }
//    $states[$node] = $status;
//}
?>
<link type="text/css" href="css/dark-hive/jquery-ui-1.8.17.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript">       
//    function updateNodes(){
<?php
//echo 'var nodes = {';
//foreach ($states as $key => $value) {
//    echo $key . ':' . $value . ',';
//}
//echo '};' . "\r";
?>//              var node;
//        for(node in nodes){
//            setNodeState(node, nodes[node]);
//        }
//    }
    function setNodeState(node, level){
        if (level > 0){
            $("#btnOn"+node).attr("class","buttonOnS");
            $("#btnOff"+node).attr("class","buttonOff");
            $("#imgOff"+node).hide();
            $("#imgOn"+node).show();
        }
        else{
            $("#btnOn"+node).attr("class","buttonOn");
            $("#btnOff"+node).attr("class","buttonOffS");
            $("#imgOff"+node).show();
            $("#imgOn"+node).hide();
        }
        $("#slider"+node).slider("value", level);
        $("#lblLevel"+node).html(level);
    }
    
    $(function(){                

        $(".zones").click(function(event){
            zone = '#' + $(this).attr("href");
            $('.zone').hide();
            $(zone).show();
            event.preventDefault();
        });
        
        $(".slider").slider();
                
        $(".slider").slider({
            stop: function(event, ui) {
                field = $(this).attr("name")
                value= $(this).slider( "option", "value");
                if (value > 0){
                    $("#btnOn"+node).attr("class","buttonOnS");
                    $("#btnOff"+node).attr("class","buttonOff");
                    $("#imgOff"+node).hide();
                    $("#imgOn"+node).show();
                }
                else
                {
                    $("#btnOn"+node).attr("class","buttonOn");
                    $("#btnOff"+node).attr("class","buttonOffS");
                    $("#imgOff"+node).show();
                    $("#imgOn"+node).hide();
                }
                $('#lblLevel'+node).trigger('change');
            }
        });
        
        $(".slider").slider({
            slide: function(event, ui) {
                node = $(this).attr("name")
                value= parseInt($(this).slider( "option", "value"));
                $("#lblLevel"+node).html(value);
            }
        });
        
        $(".buttonOn").click(function(){
            node = $(this).attr("name");
            if ($(this).attr("class") == "buttonOn"){
                $(this).attr("class","buttonOnS");
                $("#btnOff"+node).attr("class","buttonOff");
            }
            $("#lblLevel"+node).html(100);
            $("#slider"+node).slider("value",100);
            $("#imgOff"+node).hide();
            $("#imgOn"+node).show();
            $('#lblLevel'+node).trigger('change');
        });
        
        $(".buttonOff").click(function(){
            node = $(this).attr("name");
            if ($(this).attr("class") == "buttonOff"){
                $(this).attr("class","buttonOffS");
                $("#btnOn"+node).attr("class","buttonOn");
            }
            $("#lblLevel"+node).html(0);
            $("#slider"+node).slider("value",0);
            $("#imgOff"+node).show();
            $("#imgOn"+node).hide();
            $('#lblLevel'+node).trigger('change');
        });
            
        $(".buttonMin").click(function(){
            node = $(this).attr("name");
            value = parseInt($("#lblLevel"+node).html());
            if (value >= 10 && value <= 100){
                value -=10;
                $("#lblLevel"+node).html(value);
                $("#slider"+node).slider("value",value);
                if (value<10){
                    $("#btnOn"+node).attr("class","buttonOn");
                    $("#btnOff"+node).attr("class","buttonOffS");
                    $("#imgOff"+node).show();
                    $("#imgOn"+node).hide();
                }
                $('#lblLevel'+node).trigger('change');
            }
        });

        $(".buttonPlus").click(function(){
            node = $(this).attr("name");
            value = parseInt($("#lblLevel"+node).html());
            if (value >= 0 && value < 100){
                value +=10;
                $("#lblLevel"+node).html(value);
                $("#btnOn"+node).attr("class","buttonOnS");
                $("#btnOff"+node).attr("class","buttonOff");
                $("#slider"+node).slider("value",value);
                $("#imgOff"+node).hide();
                $("#imgOn"+node).show();
                $('#lblLevel'+node).trigger('change');
            }
        });
                    
        $(".level").change(function(){
            node = $(this).attr("name");
            value = parseInt($("#lblLevel"+node).html());
            type = $("#type"+node).val();
            $.post("controller.php", { Operation: 'Command', Node: node, Level: value, Type: type },
            function(data) {
                $("#response").html(data);
            });
        })

        //updateNodes();
    });
</script>
<?php MPage::EndBlock("scripts") ?>

<?php
$zwaveServer = new ZwaveServer(ZWAVE_HOST, ZWAVE_PORT);

//get list of devices
$zwaveServer->send("ALIST");
$list = $zwaveServer->read();
$list = substr($list, 0, strlen($list) - 1);
$zwaveServer->close();

$devicesList = explode("#", $list);
$zones = "";
foreach ($devicesList as $device) {
    $device = explode("~", $device);
    $zones .= $device["3"] . "~";
}

$dataobject = new Dataclass();
?>

<?php MPage::BeginBlock() ?> 
<?php
$dsRooms = $dataobject->get_Rooms();
$count = 1;
while ($floor = $dsRooms->fetch()) {
    $zone = $floor["tbl_room"];
    echo "\t" . '<div class="zone" id="zone' . $count . '">' . "\n";
    echo "\t" . '<div class="zonetitle">' . $zone . '</div>' . "\n";
    echo "\t" . '<div class="zonedetails">' . "\n";
    $count++;
    echo '<ul id="node">';
    foreach ($devicesList as $device) {
        $device = explode("~", $device);
        if ($zone != null) {
            if ($device[3] == $zone) {
                ?>
                <li>
                    <?php
                    switch ($device[4]) {
                        case ($device[4] == "Binary Switch" || $device[4] == "Binary Power Switch"):
                            ?>
                            <div class="nodestate">
                                <table>
                                    <tr>
                                        <td colspan="3"><div class="nodename"><?php echo $device[1] ?></div></td>
                                    </tr>
                                    <tr>
                                        <td width="32">
                                            <img class="bulb" id="imgOff<?php echo $device[2] ?>" src="images/OffLamp240.png" alt=""/>
                                            <img class="bulb" id="imgOn<?php echo $device[2] ?>" src="images/OnLamp240.png" alt="" style="display:none;"/>
                                        </td>
                                        <td width="50">
                                            <span id="lblLevel<?php echo $device[2] ?>" name="<?php echo $device[2] ?>" class="level">0</span>
                                            <input type="hidden" name="<?php echo $device[2] ?>" id="type<?php echo $device[2] ?>" value="Binary"/>
                                        </td>
                                        <td><input type="button" name="<?php echo $device[2] ?>" id="btnOn<?php echo $device[2] ?>" class="buttonOn"/></br><input type="button" id="btnOff<?php echo $device[2] ?>" name="<?php echo $device[2] ?>" class="buttonOff"/></td>
                                    </tr>

                                </table>
                            </div>

                            <?php
                            break;

                        case ($device[4] == "Multilevel Switch" || $device[4] == "Multilevel Power Switch"):
                            ?>
                            <div class="nodestate">
                                <table>
                                    <tr>
                                        <td colspan="4"><div class="nodename"><?php echo $device[1] ?></div></td>
                                    </tr>
                                    <tr>
                                        <td width="32">
                                            <img class="bulb" id="imgOff<?php echo $device[2] ?>" src="images/OffLamp240.png" alt=""/>
                                            <img class="bulb" id="imgOn<?php echo $device[2] ?>" src="images/OnLamp240.png" alt="" style="display:none;"/>
                                        </td>
                                        <td width="50">
                                            <span id="lblLevel<?php echo $device[2] ?>" name="<?php echo $device[2] ?>" class="level">0</span>
                                            <input type="hidden" name="<?php echo $device[2] ?>" id="type<?php echo $device[2] ?>" value="Multilevel"/>
                                        </td>
                                        <td><input type="button" name="<?php echo $device[2] ?>" id="btnOn<?php echo $device[2] ?>" class="buttonOn"/></br><input type="button" id="btnOff<?php echo $device[2] ?>" name="<?php echo $device[2] ?>" class="buttonOff"/></td>
                                        <td><input type="button" name="<?php echo $device[2] ?>" id="btnPlus<?php echo $device[2] ?>" class="buttonPlus"/></br><input type="button" name="<?php echo $device[2] ?>" id="btnMin<?php echo $device[2] ?>" class="buttonMin"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><div class="slider" id="slider<?php echo $device[2] ?>" name="<?php echo $device[2] ?>"></div></td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            break;
                    }
                    ?>
                </li>
                <?php
            }
        }
    }
    echo '</ul>';
    echo "\t" . '</div></div>' . "\n";
}
?>
<?php MPage::EndBlock("body") ?>
<?php MPage::Render("MDash.php") ?> 
