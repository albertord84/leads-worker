<?php

ini_set('xdebug.var_display_max_depth', 64);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 8024);


class Welcome extends CI_Controller {
    
    public function index() {
        echo 'ola';
    }

}
