<?php require_once("config.php") ?>
<?php MPage::BeginBlock() ?>
<link type="text/css" href="css/dark-hive/jquery-ui-1.8.17.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript">   
    function initMenu() {
        $('#menu ul').hide();
        $('#menu ul:first').show();
        $('#menu li a').click(
        function() {
            var checkElement = $(this).next();
            if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
                return false;
            }
            if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
                $('#menu ul:visible').slideUp('normal');
                checkElement.slideDown('normal');
                return false;
            }
        }
    );
    }
    
    $(function(){          

        initMenu();

        $(".devices").click(function(event){
            device = '#' + $(this).attr("href");
            $('.node').hide();
            $(device).show();
            event.preventDefault();
        });   
                    
        $(".form").submit(function(){            
            $.post("controller.php", $(this).serialize(),
            function(data) {
                $("#response").html(data);
            });
            event.preventDefault();            
        })            
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
?>
<?php MPage::BeginBlock() ?> 
<div id="rooms">
    <div class="menutitle">Rooms</div>
    <ul id="menu">
        <?php
        $dataobject = new Dataclass();
        $count = 1;
        $dsRooms = $dataobject->get_Rooms();

        while ($room = $dsRooms->fetch()) {
            echo "\t\t" . '<li><a href="">' . $room["tbl_room"] . '</a>' . "\n";
            echo '<ul>';

            foreach ($devicesList as $device) {
                $device = explode("~", $device);
                if ($device[3] == $room["tbl_room"]) {
                    echo '<li><a class="devices" href="device' . $device[2] . '">' . $device[1] . '</a></li>';
                    $count++;
                }
            }
            echo "</ul></li>";
        }
        ?>
    </ul>
</div>
<?php MPage::EndBlock("sidemenu") ?>
<?php MPage::BeginBlock() ?>
<?php
$count = 0;
foreach ($devicesList as $device) {
    $device = explode("~", $device);
    ?>        
<div class="node" id="device<?php echo $device[2] ?>" <?php if ($count==0) echo 'style="display:block;"'; $count++;?>>
    <div class="nodetitle"><?php echo $device[1]; ?></div>
    <div class="nodedetails">
        <form method="post" action="" class="form">
            <table>
                <tr>
                    <th>Device Id</th>
                    <td><input type="text" size="3" disabled="true" value="<?php echo $device[2] ?>"/></td>
                </tr>
                <tr>
                    <th>Device Name</th>
                    <td><input type="text" name="NodeName" value="<?php echo $device[1] ?>"/></td>
                </tr>
                <tr>
                    <th>Device Type</th>
                    <td><input type="text" disabled="true" value="<?php echo $device[4] ?>"/></td>
                </tr>
                <tr>
                    <th>Room</th>
                    <td>
                        <select name="NodeZone">                            
                            <?php
                            $dsRooms = $dataobject->get_Rooms();
                            while ($room = $dsRooms->fetch()) {
                                if ($device[3] == $room["tbl_room"])
                                    echo '<option selected value="' . $room["tbl_room"] . '">' . $room["tbl_room"] . '</option>';
                                else
                                    echo '<option value="' . $room["tbl_room"] . '">' . $room["tbl_room"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>                
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="hidden" name="Node" value="<?php echo $device[2] ?>"/>                        
                        <input type="hidden" name="Operation" value="Update"/>                        
                        <input type="submit" class="button" name="btn" value="Update"/>
                    </td>
                </tr>
            </table>

        </form>            
    </div>
</div>
    <?php
    $count++;
}
?>

<?php MPage::EndBlock("body") ?>
<?php MPage::Render("MDefault.php") ?> 