<?php require_once("config.php") ?>
<?php
if (isset($_POST["Operation"])) {

    include "Ssh2_crontab_manager.php";

    if ($_POST["Operation"] != "Delete") {
        if (isset($_POST["min"]))
            $min = $_POST["min"];
        else
            $min = "*";

        if (isset($_POST["hr"]))
            $hr = $_POST["hr"];
        else
            $hr = "*";

        if (isset($_POST["tmin"]))
            $tmin = $_POST["tmin"];
        else
            $tmin = "*";

        if (isset($_POST["thr"]))
            $thr = $_POST["thr"];
        else
            $thr = "*";

        if (isset($_POST["date"])) {
            $pos = strpos($_POST["date"], "/");
            $month = substr($_POST["date"], 0, $pos);
            $day = substr($_POST["date"], $pos + 1, strrpos($_POST["date"], "/") - 3);
        } else {
            $month = "*";
            $day = "*";
        }

        $state = $_POST["state"];
        $deviceId = $_POST["device"];
    }
    $dataobject = new Dataclass();
    try {
        $crontab = new Ssh2_crontab_manager('localhost', '22', 'zwave', 'password');
        switch ($_POST["Operation"]) {
            case "Add":

                $jobOn = "";
                if (isset($_POST["repeat"])) {
                    foreach ($_POST["repeat"] as $dayOfWeek) {
                        $month = "*";
                        $day = "*";

                        if ($state != "0") {
                            $jobOn = $min . ' ' . $hr . ' ' . $day . ' ' . $month . ' ' . $dayOfWeek . ' curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $deviceId . '&level=' . $state . '"';
                            $crontab->append_cronjob($jobOn);
                        }

                        $jobOff = $tmin . ' ' . $thr . ' ' . $day . ' ' . $month . ' ' . $dayOfWeek . ' curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $deviceId . '&level=0"';
                        $crontab->append_cronjob($jobOff);
                    }
                    //save job to database
                    $dataobject->insert_Schedule($_POST["summary"], $jobOn, $jobOff, implode(",", $_POST["repeat"]));
                } else {
                    //set one time event
                    if ($state != "0") {
                        $jobOn = $min . ' ' . $hr . ' ' . $day . ' ' . $month . ' * curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $deviceId . '&level=' . $state . '"';
                        $crontab->append_cronjob($jobOn);
                    }

                    $jobOff = $tmin . ' ' . $thr . ' ' . $day . ' ' . $month . ' * curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $deviceId . '&level=0"';
                    $crontab->append_cronjob($jobOff);
                    //save job to database
                    $dataobject->insert_Schedule($_POST["summary"], $jobOn, $jobOff, "");
                }

                break;

            case "Update":
                if (isset($_POST["repeat"]))
                    $repeat = implode(",", $_POST["repeat"]);
                else
                    $repeat = "";

                $dayOfWeek = "*";
                $jobOff = $tmin . ' ' . $thr . ' ' . $day . ' ' . $month . ' ' . $dayOfWeek . ' curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $deviceId . '&level=' . $state . '"';
                $jobOn = $min . ' ' . $hr . ' ' . $day . ' ' . $month . ' ' . $dayOfWeek . ' curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $deviceId . '&level=' . $state . '"';
                $result = $dataobject->update_Schedule($_POST["jobID"], $_POST["summary"], $jobOn, $jobOff, $repeat);

                //clear crontab
                //$crontab->remove_crontab();
                break;

            case "Delete":
                $result = $dataobject->delete_Schedule($_POST["jobID"]);
                //clear crontab
                $crontab->remove_crontab();

                //re create jobs in crontab
                $dsJobs = $dataobject->get_Schedules();
                while ($job = $dsJobs->fetch()) {
                    $repeats = explode(",",$job["schedule_repeat"]);
                    foreach($repeats as $dayOfWeek) {
                        if ($job["schedule_on"] != null) {
                            $detailsOn = parseCommand($job["schedule_on"]);
                            $jobOn = $detailsOn["min"] . ' ' . $detailsOn["hr"] . ' ' . $detailsOn["day"] . ' ' . $detailsOn["month"] . ' '.$dayOfWeek.' curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $detailsOn["node"] . '&level=' . $detailsOn["level"] . '"';
                            $crontab->append_cronjob($jobOn);
                        }
                        if ($job["schedule_off"] != null) {
                            $detailsOff = parseCommand($job["schedule_off"]);
                            $jobOff = $detailsOff["min"] . ' ' . $detailsOff["hr"] . ' ' . $detailsOff["day"] . ' ' . $detailsOff["month"] . ' '.$dayOfWeek.' curl "http://' . URI . '/zwave/server.php?command=control&type=binary&node=' . $detailsOff["node"] . '&level=' . $detailsOff["level"] . '"';
                            $crontab->append_cronjob($jobOff);
                        }
                    }
                }
                break;
        }
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
}

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

$zones = substr($zones, 0, strlen($zones) - 1);
$zones = explode("~", $zones);
$zones = array_unique($zones);
?>
<?php MPage::BeginBlock() ?>
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
        
        var devices = "<?php echo $list; ?>";
        var devicesList = devices.split("#");

        $(".job").click(function(event){                 
            panel = '#' + $(this).attr("href");
            $('.node').hide();
            $(panel).show();
            event.preventDefault();
        });   

        $("#datepicker" ).datepicker();
  
        $("#zone").change(function(){           
            $("#device").children().remove();
            $('#device').append('<option value="">Select</option>');
            var x;            
            for (x in devicesList)
            {   
                var device = devicesList[x].split("~");
                if ($(this).val() == device[3])
                    $('#device').append('<option value="' + device[2] + '">' + device[1] + '</option>');                
            }           
        
        });
        
        $("#device").change(function(){
            $("#state").children().remove();
            $('#state').append('<option value="">Select</option>');
            for (x in devicesList)
            {
                var device = devicesList[x].split("~");
                if ($(this).val() == device[2])
                    switch (device[4]){
                        case "Multilevel Switch":
                            for(i=0; i<=100; i++){
                                $('#state').append('<option value="' + i + '">' + i + '</option>');                
                                i+=9;
                            }
                        break;
                        
                    case "Multilevel Power Switch":
                        for(i=0; i<=100; i++){
                            $('#state').append('<option value="' + i + '">' + i + '</option>');                
                            i+=9;
                        }
                        break;
                        
                    case "Binary Switch":
                        $('#state').append('<option value="0">Off</option>');                
                        $('#state').append('<option value="255">On</option>');                
                        break;
                        
                    case "Binary Power Switch":
                        $('#state').append('<option value="0">Off</option>');                
                        $('#state').append('<option value="255">On</option>');                
                        break;
                        
                    case "Routing Binary Sensor":
                        $('#state').append('<option value="0">Off</option>');                
                        $('#state').append('<option value="255">On</option>');                
                        break;
                    
                }            
            }
        }); 
        
    });
    
</script>
<?php MPage::EndBlock("scripts") ?>
<?php MPage::BeginBlock() ?>
<div id="rooms">
    <div class="menutitle">Scheduled Jobs</div>
    <ul id="menu">
        <?php
        $dataobject = new Dataclass();


        foreach ($zones as $zone) {
            if ($zone != null) {
                echo '<li><a href="#">' . $zone . '</a>';
                echo '<ul>';
                foreach ($devicesList as $device) {
                    $device = explode("~", $device);
                    if ($device[3] == $zone) {
                        $dsSchedules = $dataobject->get_Schedules();
                        while ($job = $dsSchedules->fetch()) {
                            //get node
                            if ($job["schedule_on"] != null)
                                $details = parseCommand($job["schedule_on"]);
                            else
                                $details = parseCommand($job["schedule_off"]);

                            //echo "node" .'='. $device[2] . ", " . $details["node"] ."<br>";
                            if ($device[2] == $details["node"])
                                echo '<li><a class="job" href="job' . $job["idtbl_schedules"] . '">' . $job["schedule_summary"] . '</a></li>';
                        }
                    }
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        ?>
        <li><a style="color:#f6b125" class="job" href="job0">+ New Job</a></li>
    </ul>
</div>
<?php MPage::EndBlock("sidemenu") ?>
<?php MPage::BeginBlock() ?> 
<div class="node" id="job0" style="display:block;">
    <div class="nodetitle">Job</div>
    <div class="nodedetails">
        <form method="post" action="" class="form">
            <table>
                <tr>
                    <th>Summary</th>
                    <td><input type="text" id="summary" name="summary" style="width:300px;"/></td>
                </tr>
                <tr>
                    <th>Zone</th>
                    <td>
                        <select id="zone" name="zone">
                            <option value="">Select</option>';
                            <?php
                            foreach ($zones as $zone) {
                                if ($zone == null)
                                    echo '<option value="">Undefined</option>';
                                else
                                    echo '<option value="' . $zone . '">' . $zone . '</option>';
                            }
                            ?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <th>Device</th>
                    <td><select id="device" name="device"></select></td>
                </tr>

                <tr>
                    <th>State</th>
                    <td><select id="state" name="state"></select></td>
                </tr>
                <tr>

                    <th>Time On</th>
                    <td>
                        <select name="hr" id="hr">
                            <?php
                            for ($i = 1; $i <= 23; $i++) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                        :
                        <select name="min" id="min">
                            <?php
                            for ($i = 0; $i <= 59; $i++) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <th>Time Off</th>
                    <td>
                        <select name="thr" id="hr">
                            <?php
                            for ($i = 1; $i <= 23; $i++) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                        :
                        <select name="tmin" id="min">
                            <?php
                            for ($i = 0; $i <= 59; $i++) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </td>                      
                    </td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><input type="text" id="datepicker" name="date"/></td>
                </tr>
<!--                <tr>
                    <th>Repeat</th>
                    <td><input type="checkbox" id="btnRepeat" name="repeat"/></td>
                </tr>-->
                <tr>
                    <th>Repeat on</th>
                    <td>
                        <?php
                        $days = array("S", "M", "T", "W", "T", "F", "S");

                        foreach ($days as $key => $day) {
                            echo '<input type="checkbox" value="' . $key . '" id="repeat" name="repeat[]"/>' . $day . ' ';
                        }
                        ?>

                    </td>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <td>
                        <input type="hidden" name="Operation" value="Add"/>
                        <input type="submit" class="button" name="submit" value="Save"/>
                    </td>
                </tr>
            </table>

        </form>
    </div>
</div>
<?php

function parseCommand($strCommand) {
    //get time/date details
    $dateTime = substr($strCommand, 0, strpos($strCommand, "curl") - 1);
    $dateTime = explode(" ", $dateTime);

    $details["min"] = $dateTime[0];
    $details["hr"] = $dateTime[1];
    $details["day"] = $dateTime[2];
    $details["month"] = $dateTime[3];
    $details["dayofweek"] = $dateTime[4];

    //get node details
    $strCommand = substr($strCommand, strpos($strCommand, ".php?") + 5);
    $strCommand = substr($strCommand, 0, strlen($strCommand)-1);
    $tmp = explode("&", $strCommand);
    foreach ($tmp as $attribute) {
        $tmp = explode("=", $attribute);
        $details[$tmp[0]] = $tmp[1];
    }
    return $details;
}

$dsSchedules = $dataobject->get_Schedules();
while ($dsJob = $dsSchedules->fetch()) {
    $jobDate = null;
    $state = null;
    $type = null;
    if ($dsJob["schedule_on"]) {
        $detailsOn = parseCommand($dsJob["schedule_on"]);
        //print_r($detailsOn);
        echo $detailsOn["level"];
        if ($detailsOn["month"] != '*' && $detailsOn["day"] != '*') {
            $jobDate = $detailsOn["month"] . '/' . $detailsOn["day"] . '/' . date("y");
            $state = $detailsOn["level"];
            $type = $detailsOn["type"];
            
        }
    }
    if ($dsJob["schedule_off"]) {
        $detailsOff = parseCommand($dsJob["schedule_off"]);
        if ($detailsOff["month"] != '*' && $detailsOff["day"] != '*') {
            $jobDate = $detailsOff["month"] . '/' . $detailsOff["day"] . '/' . date("y");
            //$state = $detailsOff["level"];
            //$type = $detailsOff["type"];
        }
    }
    
    $repeats = explode(",", $dsJob["schedule_repeat"]);
    ?>
    <div class="node" id="job<?php echo $dsJob["idtbl_schedules"] ?>">
        <div class="nodetitle">Job</div>
        <div class="nodedetails">
            <form method="post" action="" class="form">
                <table>
                    <tr>
                        <th>Summary</th>
                        <td><input type="text" id="summary" name="summary" value="<?php echo $dsJob["schedule_summary"] ?>" style="width:300px;"/></td>
                    </tr>
                    <tr>
                        <th>Zone</th>
                        <td>
                            <select id="zone" name="zone">
                                <option value="">Select</option>';
                                <?php
                                foreach ($zones as $zone) {
                                    foreach ($devicesList as $device) {
                                        $device = explode("~", $device);
                                        if ($detailsOn["node"] == $device[2])
                                            $currentZone = $device[3];
                                    }
                                    if ($zone == null)
                                        echo '<option value="">Undefined</option>';
                                    else {
                                        if ($zone == $currentZone)
                                            echo '<option value="' . $zone . '" selected>' . $zone . '</option>';
                                        else
                                            echo '<option value="' . $zone . '">' . $zone . '</option>';
                                    }
                                }
                                ?>
                            </select>

                        </td>
                    </tr>
                    <tr>
                        <th>Device</th>
                        <td>
                            <select id="device" name="device">
                                <?php
                                foreach ($devicesList as $device) {
                                    $device = explode("~", $device);
                                    if ($detailsOn["node"] == $device[2])
                                        echo '<option value="' . $device[2] . '" selected>' . $device[1] . '</option>';
                                    else
                                        echo '<option value="' . $device[2] . '">' . $device[1] . '</option>';
                                }
                                ?>
                            </select></td>
                    </tr>

                    <tr>
                        <th>State</th>
                        <td><select id="state" name="state">
                                <?php
                                //echo $state . " " . $level;
                                switch ($type) {
                                    case "Multilevel Switch":
                                        for ($i = 0; $i <= 100; $i++) {
                                            if ($state == $i)
                                                echo '<option selected value="' . $i . '">' . $i . '</option>';
                                            else
                                                echo '<option value="' . $i . '">' . $i . '</option>';
                                            $i+=9;
                                        }
                                        break;

                                    case "Multilevel Power Switch":
                                        for ($i = 0; $i <= 100; $i++) {
                                            if ($state == $i)
                                                echo '<option selected value="' . $i . '">' . $i . '</option>';
                                            else
                                                echo '<option value="' . $i . '">' . $i . '</option>';
                                            $i+=9;
                                        }
                                        break;

                                    case "binary":
                                        if ($state == "Off")
                                            echo '<option value="0" selected>Off</option>';
                                        else
                                            echo '<option value="0">Off</option>';

                                        if ($state == "On")
                                            echo '<option value="255" selected>On</option>';
                                        else
                                            echo '<option value="255">On</option>';
                                        break;

                                    case "Binary Power Switch":
                                        echo '<option value="0">Off</option>';
                                        echo '<option value="255">On</option>';
                                        break;

                                    case "Routing Binary Sensor":
                                        echo '<option value="0">Off</option>';
                                        echo '<option value="255">On</option>';
                                        break;
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>

                        <th>Time On</th>
                        <td>
                            <select name="hr" id="hr">
                                <?php
                                for ($i = 1; $i <= 23; $i++) {
                                    if ($detailsOn["hr"] == $i)
                                        echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                    else
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                                ?>
                            </select>
                            :
                            <select name="min" id="min">
                                <?php
                                for ($i = 0; $i <= 59; $i++) {
                                    if ($detailsOn["min"] == $i)
                                        echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                    else
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <th>Time Off</th>
                        <td>
                            <select name="thr" id="hr">
                                <?php
                                for ($i = 1; $i <= 23; $i++) {
                                    if ($detailsOff["hr"] == $i)
                                        echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                    else
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                                ?>
                            </select>
                            :
                            <select name="tmin" id="min">
                                <?php
                                for ($i = 0; $i <= 59; $i++) {
                                    if ($detailsOff["min"] == $i)
                                        echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                    else
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        </td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td><input type="text" id="datepicker" name="date" value="<?php echo $jobDate; ?>"/></td>
                    </tr>
    <!--                        <tr>
                        <th>Repeat</th>
                        <td><input type="checkbox" id="btnRepeat" name="repeat"/></td>
                    </tr>-->
                    <tr>
                        <th>Repeat on</th>
                        <td>
                            <?php
                            $days = array("S", "M", "T", "W", "T", "F", "S");

                            foreach ($days as $key => $day) {
                                if (in_array($key, $repeats))
                                    echo '<input type="checkbox" checked value="' . $key . '" id="repeat" name="repeat[]"/>' . $day . ' ';
                                else
                                    echo '<input type="checkbox" value="' . $key . '" id="repeat" name="repeat[]"/>' . $day . ' ';
                            }
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <td>
                            <input type="hidden" name="jobID" value="<?php echo $dsJob["idtbl_schedules"] ?>"/>
                            <input type="hidden" name="Operation" value="Update"/>
                            <input type="submit" class="button" name="submit" value="Update"/>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="" class="form">
                                <input type="hidden" name="jobID" value="<?php echo $dsJob["idtbl_schedules"] ?>"/>
                                <input type="hidden" name="Operation" value="Delete"/>
                                <input type="submit" class="button" name="submit" value="Delete"/>
                            </form>
                        </td>
                    </tr>
                </table>
        </div>
    </div>
    <?php
}
?>
<?php MPage::EndBlock("body") ?>
<?php MPage::Render("MDefault.php") ?> 