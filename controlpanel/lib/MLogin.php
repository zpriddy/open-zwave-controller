<?php echo '<? xml version="1.0" encoding="UTF-8" ?>';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html version="-//W3C//DTD XHTML 1.1//EN"
      xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.w3.org/1999/xhtml
      http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
      >
    <head>
        <title><?php echo TITLE ?></title>
        <link rel="stylesheet" type="text/css" href="css/default.css" />
        <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
        <?php
        if (MPage::IsDefined("stylesheet")) {
            echo MPage::PlaceHolder("stylesheet");
        }

        if (MPage::IsDefined("scripts")) {
            echo MPage::PlaceHolder("scripts");
        }
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <div id="wrapper">
            <div id="login">
                <?php
                if (MPage::IsDefined("body")) {
                    echo MPage::PlaceHolder("body");
                }
                ?>
            </div>            
        </div>
    </body>
</html>