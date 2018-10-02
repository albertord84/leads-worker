<?php

class profiles_status {

    const ACTIVE = 1;
    const ENDED = 2;
    const CANCELED = 3;
    const MISSING = 4;

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
