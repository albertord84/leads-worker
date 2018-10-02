<?php

class payment_type {

    const CREDIT_CARD = 1;
    const TICKET_BANK = 2;
    
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
