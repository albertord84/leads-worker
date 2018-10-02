<?php

class campaing_status {

    const CREATED = 1;
    const ACTIVE = 2;
    const PAUSED = 3;
    const DELETED = 4;
    const ENDED = 5;

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
