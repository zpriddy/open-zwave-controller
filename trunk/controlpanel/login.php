<?php require_once("config.php") ?> 
<?php
if (isset($_POST["txtUsername"]) && isset($_POST["txtPassword"])) {
    $dataclass = new Dataclass();
    $result = $dataclass->check_Login($_POST["txtUsername"], sha1($_POST["txtPassword"]));
    $valid = false;

    $userID = 0;
    while ($row = $result->fetch()) {
        //Login ok
        $userID = $row["idtbladmin"];
        $_SESSION["UserId"] = $userID;
        $_SESSION["Username"] = $row["username"];
        $valid = true;
    }
    //
    if (isset($_POST["chkRemember"])) {
        setcookie("Username", $_POST["txtUsername"], EXPIRE);
        setcookie("Password", $_POST["txtPassword"], EXPIRE);
    }

    if (isset($_POST["chkKeepSignedIn"])) {
        setcookie("SignedIn", "true", EXPIRE);
        setcookie("UserId", $userID, EXPIRE);
        setcookie("Username", $_POST["txtUsername"], EXPIRE);
    }
}

if (isset($_SESSION["Username"]))
    header("location:index.php");
?>
<?php MPage::BeginBlock() ?>
<div class="nodetitle">Login</div>
<div class="nodedetails">
    <div style="background-color:#222; float:left; margin:20px; border:solid 1px #444; padding:30px; width:260px;">         
        <?php include "inc/logo.php"; ?>
    </div>
    <form method="post" action="" name="frmLogin" id="frmLogin" class="form">
        <table>
            <tr>
                <th>Username</th>
                <td><input type="text" name="txtUsername" id="txtUsername" value="<?php if (isset($_COOKIE["Username"]))
            echo $_COOKIE["Username"]; ?>"/></td>
            </tr>
            <tr>
                <th>Password</th>
                <td><input type="password" name="txtPassword" id="txtPassword" value="<?php if (isset($_COOKIE["Password"]))
                               echo $_COOKIE["Password"]; ?>"/></td>
            </tr>
            <tr>
                <td colspan="2"><input type="checkbox" name="chkRemember" id="chkRemember" value="true" checked/>Remember me?</br>
                    <input type="checkbox" name="chkKeepSignedIn" id="chkKeepSignedIn" value="true"/>Keep me signed in?</td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="button" value="Login"/></td>
            </tr>
            <tr>
                <td colspan="2"><?php if (isset($valid) && !$valid)
                               echo "Invalid login details!"; ?></td>
            </tr>
        </table>
    </form>
</div>

<?php MPage::EndBlock("body") ?>
<?php MPage::Render("MLogin.php") ?>