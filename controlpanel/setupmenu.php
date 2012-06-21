<?php
include "config.php";
?>
<div class="menutitle">Settings</div>
<ul id="menu">
    <li><a href="#">Admin</a>
        <ul>
            <li><a class="setupwindow" href="password">Change Password</a></li>
        </ul>
    </li>
    <li><a href="#">Floors</a>
        <ul>
            <?php
            $dataobject = new Dataclass();
            $dsFloors = $dataobject->get_Floors();
            $count = 1;
            while ($floor = $dsFloors->fetch()) {
                echo '<li><a class="setupwindow" href="floor' . $floor["idtbl_floor"] . '">' . $floor["tbl_floor"] . '</a></li>';
                $count++;
            }
            ?>
            <li><a style="color:#f6b125" class="setupwindow" href="floor0">+ New Floor</a></li>
        </ul>        
    </li>
    <li><a href="#">Rooms</a>
        <ul>
            <?php
            $dataobject = new Dataclass();
            $count = 1;
            $dsRooms = $dataobject->get_Rooms();
            while ($room = $dsRooms->fetch()) {
                echo '<li><a class="setupwindow" href="room' . $room["idtbl_rooms"] . '">' . $room["tbl_room"] . '</a></li>';
                $count++;
            }
            ?>
            <li><a style="color:#f6b125" class="setupwindow" href="room0">+ New Room</a></li>
        </ul>        
    </li>
</ul>