<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace leads\cls{

/**
 * Utils for general purposes
 *
 * @author albertord
 */
class Utils {
    
    static public function extractEmail($text) {
        $text = strtolower($text);
        $emails = array();
        // this regex handles more email address formats like a+b@google.com.sg, and the i makes it case insensitive
//        $pattern = "/[a-z0-9_\-\+']+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i";
        $pattern = "/[a-z0-9]+[_a-z0-9\'\.-]*[a-z0-9]+@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/i";

        // preg_match_all returns an associative array
        preg_match_all($pattern, $text, $emails);

        // the data you want is in $matches[0], dump it with var_export() to see it
        //$a = var_export($emails[0]);
        if(isset($emails[0][0]))
            return $emails[0][0];
        else
            return null;
    }
}

}