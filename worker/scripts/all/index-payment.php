<?PHP

require_once '../../class/Worker.php';
require_once '../../class/system_config.php';
require_once '../../class/Gmail.php';
require_once '../../class/Payment.php';
require_once '../../class/Daily_Payment.php';


echo "Payment-Robot Inited...!<br>\n";
echo date("Y-m-d h:i:sa");

$GLOBALS['sistem_config'] = new leads\cls\system_config();

// WORKER
$Daily_Payment = new leads\cls\Daily_Payment();

$Daily_Payment->check_payment_leads();


$Gmail = new leads\cls\Gmail();
$Gmail->send_mail("jorge85.mail@gmail.com", "Jorge Moreno ",'DUMBU-LEADS payment checked!!! ','DUMBU-LEADS payment checked!!! ');
$Gmail->send_mail("josergm86@gmail.com", "Jose Ramon ",'DUMBU-LEADS payment checked!!! ','DUMBU-LEADS payment checked!!! ');

echo "\n<br>" . date("Y-m-d h:i:sa") . "\n\n";