<?php
require_once("config.php");
$dataobject = new Dataclass();

if (isset($_POST["floorid"])) {
    $dataobject->update_floor($_POST["floorid"], $_POST["FloorName"]);
    echo $_POST["FloorName"] . " Updated";
} else {
    ?>

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
                                                                          
        function getSideMenu(){
            $.get("setupmenu.php",
            function(data) {
                $("#sidemenu").html(data);
                                            
                initMenu(); 
                                                        
                $(".setupwindow").click(function(event){                
                    panel = '#' + $(this).attr("href");
                    $('.node').hide();
                    $(panel).show();
                    event.preventDefault();
                });         
            });
        }                              
                                                                          
        $(function(){          
            getSideMenu();                                                                                                                       
                                        
            $(".form").submit(function(){            
                action = $(this).attr("action");
                $.post(action, $(this).serialize(),
                function(data) {
                    $("#response").html(data);
                    getSideMenu();
                });
                event.preventDefault();                                                               
            });                                                  
        }); 
    </script>
    <?php MPage::EndBlock("scripts") ?>
    <?php MPage::BeginBlock() ?>    
    <?php MPage::EndBlock("sidemenu") ?>
    <?php MPage::BeginBlock() ?>
    <div class="node" id="password" style="display: block;">
        <div class="nodetitle">Change Password</div>
        <div class="nodedetails">
            <form method="post" action="password.php" class="form"> 
                <table>
                    <tr>
                        <th>Password</th>
                        <td><input type="password" name="txtPassword"/></td>
                    </tr>
                    <tr>
                        <th>Re-type Password</th>
                        <td><input type="password" name="txtPassword2"/></td>
                    </tr>    
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" class="button" value="Update"/></td>
                    </tr>
                </table>    
            </form>
        </div>
    </div>
    <div class="node" id="floor0">
        <div class="nodetitle">Add Floor</div>
        <div class="nodedetails">
            <form method="post" action="floors.php" class="form">
                <table>
                    <tr>
                        <th>Floor Name</th>
                        <td><input type="text" name="FloorName" value=""/></td>
                    </tr>                
                    <tr>
                        <td>&nbsp;</td>
                        <td>                                                   
                            <input type="hidden" name="Operation" value="Add"/>                        
                            <input type="submit" class="button" name="btn" value="Add"/>
                        </td>
                    </tr>
                </table>
            </form>            
        </div>
    </div>
    <?php
    $dsFloors = $dataobject->get_Floors();
    while ($floor = $dsFloors->fetch()) {
        ?>

        <div class="node" id="floor<?php echo $floor["idtbl_floor"] ?>">
            <div class="nodetitle"><?php echo $floor["tbl_floor"]; ?></div>
            <div class="nodedetails">
                <form method="post" action="floors.php" class="form">
                    <table>
                        <tr>
                            <th>Floor Name</th>
                            <td colspan="2"><input type="text" name="FloorName" value="<?php echo $floor["tbl_floor"] ?>"/></td>
                        </tr>                
                        <tr>
                            <td>&nbsp;</td>
                            <td> <input type="hidden" name="floorid" value="<?php echo $floor["idtbl_floor"] ?>"/>                        
                                <input type="hidden" name="Operation" value="Update"/>                        
                                <input type="submit" class="button" name="btn" value="Update"/>
                                </form></td>
                            <td>

                                <form method="post" action="floors.php" class="form">
                                    <input type="hidden" name="FloorName" value="<?php echo $floor["tbl_floor"] ?>"/>
                                    <input type="hidden" name="floorid" value="<?php echo $floor["idtbl_floor"] ?>"/>                        
                                    <input type="hidden" name="Operation" value="Delete"/>   
                                    <input type="submit" class="button" name="btn" value="Delete"/>
                                </form>   
                            </td>
                        </tr>
                    </table>
            </div>
        </div>
        <?php
    }
    $dsRooms = $dataobject->get_Rooms();
    while ($room = $dsRooms->fetch()) {
        ?>
        <div class="node" id="room<?php echo $room["idtbl_rooms"] ?>">
            <div class="nodetitle"><?php echo $room["tbl_room"]; ?></div>
            <div class="nodedetails">
                <form method="post" action="rooms.php" class="form">
                    <table>
                        <tr>
                            <th>Room Name</th>
                            <td colspan="2"><input type="text" name="roomName" value="<?php echo $room["tbl_room"] ?>"/></td>
                        </tr>                
                        <tr>
                            <th>Floor</th>
                            <td colspan="2">
                                <select name="floor">
                                    <?php
                                    $dsFloors = $dataobject->get_Floors();
                                    while ($floor = $dsFloors->fetch()) {
                                        if ($floor["idtbl_floor"] == $room["tbl_floorId"])
                                            echo '<option selected value="' . $floor["idtbl_floor"] . '">' . $floor["tbl_floor"] . '</option>';
                                        else
                                            echo '<option value="' . $floor["idtbl_floor"] . '">' . $floor["tbl_floor"] . '</option>';
                                    }
                                    ?>                                
                                </select>
                            </td>
                        </tr> 
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                 <input type="hidden" name="roomid" value="<?php echo $room["idtbl_rooms"] ?>"/>                        
                                <input type="hidden" name="Operation" value="Update"/>                        
                                <input type="submit" class="button" name="btn" value="Update"/>
                                </form>                                    
                            </td>
                            <td>
                               
                                <form method="post" action="rooms.php" class="form">
                                    <input type="hidden" name="roomid" value="<?php echo $room["idtbl_rooms"] ?>"/>    
                                    <input type="hidden" name="roomName" value="<?php echo $room["tbl_room"] ?>"/>
                                    <input type="hidden" name="Operation" value="Delete"/>                        
                                    <input type="submit" class="button" name="btn" value="Delete"/>
                                </form>   
                            </td>
                        </tr>
                    </table>      
            </div>
        </div>    
    <?php } ?>
    <div class="node" id="room0">
        <div class="nodetitle">Add Room</div>
        <div class="nodedetails">
            <form method="post" action="rooms.php" class="form">
                <table>
                    <tr>
                        <th>Room Name</th>
                        <td><input type="text" name="roomName" value=""/></td>
                    </tr>                
                    <tr>
                        <th>Floor</th>
                        <td>
                            <select name="floor">
                                <?php
                                $dsFloors = $dataobject->get_Floors();
                                while ($floor = $dsFloors->fetch()) {
                                    echo '<option value="' . $floor["idtbl_floor"] . '">' . $floor["tbl_floor"] . '</option>';
                                }
                                ?>                                
                            </select>
                        </td>
                    </tr> 
                    <tr>
                        <td>
                            &nbsp;                         
                        </td>
                        <td>
                            <input type="hidden" name="Operation" value="Add"/>                        
                            <input type="submit" class="button" name="btn" value="Add"/>
                        </td>
                    </tr>
                </table>

            </form>            
        </div>
    </div>
    <?php MPage::EndBlock("body") ?>
    <?php MPage::Render("MDefault.php") ?> 
    <?php
}
?>