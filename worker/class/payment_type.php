<?php

namespace leads\cls {

    class payment_type {
        // uso geral
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

}