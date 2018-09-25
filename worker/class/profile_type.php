<?php

namespace leads\cls {

    class profile_type {

        const REFERENCE_PROFILE = 1;
        const GEOLOCATION = 2;
        const HASHTAG = 3;

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