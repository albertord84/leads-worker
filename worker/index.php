<?PHP

require_once 'class/Worker.php';
require_once 'class/system_config.php';
require_once 'class/Gmail.php';
require_once 'class/Payment.php';

//nohup sh /opt/lampp/htdocs/leads/worker/leads-worker.sh &
//nohup sh /opt/lampp/htdocs/leads/worker/scripts/all/leads-all-worker1.sh &
//nohup sh /opt/lampp/htdocs/leads/worker/scripts/part/leads-worker1.sh &
//vps4633.publiccloud.com.br (191.252.111.93)

echo "Worker Principal Leads Inited...!<br>\n";
echo date("Y-m-d h:i:sa");

$GLOBALS['sistem_config'] = new leads\cls\system_config();

// WORKER
$Worker = new leads\cls\Worker();

$Worker->truncate_daily_work();
$Worker->prepare_daily_work();


$Gmail = new leads\cls\Gmail();
//$Gmail->send_mail("edazaldi@gmail.com", "Elio Zaldivar",'DUMBU-LEADS prepare daily work done!!! ','DUMBU-LEADS prepare daily work done!!! ');
$Gmail->send_mail("jorge85.mail@gmail.com", "Jorge Moreno ",'DUMBU-LEADS prepare daily work done!!! ','DUMBU-LEADS prepare daily work done!!! ');
$Gmail->send_mail("josergm86@gmail.com", "Jose Ramon ",'DUMBU-LEADS prepare daily work done!!! ','DUMBU-LEADS prepare daily work done!!! ');

echo "\n<br>" . date("Y-m-d h:i:sa") . "\n\n";
