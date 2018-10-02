<?php

class user_status {

    const ACTIVE = 1;
    const BLOCKED_BY_PAYMENT = 2;
    const DELETED = 4;
    const PENDENT_BY_PAYMENT = 6;
    const BEGINNER= 8;
    const DONT_DISTURB= 11;

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
