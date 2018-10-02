<?php

class user_role
{
    const ADMIN = 1;
    const CLIENT = 2;
    const ATTENDET = 3;

    static public function Defines($const)
    {
        $cls = new ReflectionClass(__CLASS__);
        foreach($cls->getConstants() as $key=>$value)
        {
            if($value == $const)
            {
                return true;
            }
        }

        return false;
    }
}