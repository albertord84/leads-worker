<?php

require_once '../class/DB.php';
$db = new leads\cls\DB();

//echo str_replace("'", "\'", "Hello Wor'ld of PHP");;



//$db->set_id_in_profile();
//$db->copy_all_leads();
$db->show_all_leads();
//$db->codify_base64_all_leads();

//$seed = "mi chicho lindo";
//$key_number = md5($seed);
//$a = openssl_encrypt ('jose ramon gonzalez 86010824666', "aes-256-ctr", $key_number);
//$b = openssl_decrypt ($a, "aes-256-ctr", $key_number);
//echo $b;

//-------------------------------------------------------------------------
//require_once '../class/Utils.php';
//$utils = new \leads\cls\Utils();
//echo $utils->extractEmail("perro cagando josergm86gmail.com");

//-------------------------------------------------------------------------

//require_once '../class/Worker.php';
//$Worker = new leads\cls\Worker();
//$Worker->truncate_daily_work();


?>