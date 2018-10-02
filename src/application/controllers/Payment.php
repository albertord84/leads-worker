<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Worker extends CI_Controller {

    public function create_payment() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Payment.php';
        $Gmail = new follows\cls\Payment();
        $payment_data = urldecode($_POST['payment_data']);
        $result = $Payment->create_payment($payment_data);
        echo json_encode($result);
    }
    
    public function create_boleto_payment() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Payment.php';
        $Gmail = new follows\cls\Payment();
        $payment_data = urldecode($_POST['payment_data']);
        $result = $Payment->create_boleto_payment($payment_data);
        echo json_encode($result);
    }
    

}
