<?php

class MPage {

    static function Render($page) {
        include($page);
    }

    static function BeginBlock() {
        ob_start();
    }

    static function EndBlock($name) {
        $data = ob_get_contents();
        define("_block_" . $name . "_", $data, true);
        ob_end_clean();
    }

    static function PlaceHolder($name) {
        return constant("_block_" . $name . "_");
    }

    static function IsDefined($name) {
        return defined("_block_" . $name . "_");
    }

}

?>
