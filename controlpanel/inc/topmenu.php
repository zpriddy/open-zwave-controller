<div id="topmenu">
    <ul>

        <?php
        $links = array( 
            "Control"           => "index.php", 
            "Devices"           => "devices.php",            
            "Scheduler"         =>"scheduler.php", 
            "Setup"             =>"settings.php");
        $currentPage = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
        if (isset($_SESSION["Username"])) {
            foreach ($links as $key => $value) {
                if (strtolower($currentPage) == strtolower($value))
                    echo '<li><a href="' . $value . '" class="currentpage">' . $key . '</a></li>';
                else
                    echo '<li><a href="' . $value . '">' . $key . '</a></li>';
            }
        }
        else
            echo '<li><a href="login.php" class="currentpage">Login</a></li>';
        ?>

    </ul>
</div>
<div id="userarea">
    <?php
    if (isset($_SESSION["Username"])) {
        ?>
        <ul>
            <li><span id="user"><?php echo $_SESSION["Username"]; ?></span></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <?php
    }
    ?>
</div>
<div id="topcenter">&nbsp;</div>