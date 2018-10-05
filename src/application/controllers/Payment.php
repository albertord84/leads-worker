<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

    public function create_payment() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new leads\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Payment.php';
        $Payment = new leads\cls\Payment();
        $payment_data = $_POST['payment_data'];
        
        $payment_data['credit_card_number'] = urldecode($payment_data['credit_card_number']);
        $payment_data['credit_card_name'] = urldecode($payment_data['credit_card_name']);
        $payment_data['credit_card_cvc'] = urldecode($payment_data['credit_card_cvc']);
        
        $result = $Payment->create_payment($payment_data);
        echo json_encode($result);
    }
    
    public function create_boleto_payment() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new leads\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Payment.php';
        $Payment = new leads\cls\Payment();
        $payment_data = $_POST['payment_data'];
        
        $payment_data['DocumentNumber'] = urldecode($payment_data['DocumentNumber']);
        $payment_data['OrderReference'] = urldecode($payment_data['OrderReference']);
        $payment_data['cpf'] = urldecode($payment_data['cpf']);
        
        $result = $Payment->create_boleto_payment($payment_data);
        echo json_encode($result);
    }
    

}
