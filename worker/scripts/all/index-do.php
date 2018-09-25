<?PHP

require_once '../../class/Worker.php';
require_once '../../class/system_config.php';
require_once '../../class/Gmail.php';
require_once '../../class/Payment.php';

echo "Worker-Robot Leads Inited...!<br>\n";
echo date("Y-m-d h:i:sa");

$GLOBALS['sistem_config'] = new leads\cls\system_config();

// WORKER
$Worker = new leads\cls\Worker();

//$Worker->truncate_daily_work();
//$Worker->prepare_daily_work();

$Worker->do_work();


$Gmail = new leads\cls\Gmail();
//$Gmail->send_mail("jorge85.mail@gmail.com", "Jorge Moreno ",'DUMBU-LEADS prepare daily work done!!! ','DUMBU-LEADS prepare daily work done!!! ');
//$Gmail->send_mail("josergm86@gmail.com", "Jose Ramon ",'DUMBU-LEADS prepare daily work done!!! ','DUMBU-LEADS prepare daily work done!!! ');

echo "\n<br>" . date("Y-m-d h:i:sa") . "\n\n";