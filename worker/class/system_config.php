<?php

namespace leads\cls {
    include_once 'DB.php';

    class system_config {

        public function __construct() {
            $DB = new DB();
            $result = $DB->get_system_config_vars();
            if ($result) {
                while ($var_info = $result->fetch_array()) {
                    $this->{$var_info["name"]} = $var_info["value"];
                }
            } else {
                die("Can't load system config vars...!!");
            };
        }

        static public function Defines($const) {
            $cls = new ReflectionClass(__CLASS__);
            foreach ($cls->getConstants() as $key => $value) {
                if ($value == $const) {
                    return true;
                }
            }
            return false;
        }

    }

}
