<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gmail extends CI_Controller {

    public function send_client_contact_form() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Gmail.php';
        $Gmail = new follows\cls\Gmail();

        $username = urldecode($_POST['username']);
        $useremail = urldecode($_POST['useremail']);
        $usermsg = urldecode($_POST['usermsg']);
        $usercompany = urldecode($_POST['usercompany']);
        $userphone = urldecode($_POST['userphone']);

        $result = $Gmail->send_client_contact_form($username, $useremail, $usermsg, $usercompany, $userphone);
        echo json_encode($result);
    }
    
    public function send_recovery_pass() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Gmail.php';
        $Gmail = new follows\cls\Gmail();

        $useremail = urldecode($_POST['useremail']);
        $username = urldecode($_POST['username']);
        $token = urldecode($_POST['token']);
        $lang = urldecode($_POST['lang']);

        $result = $Gmail->send_client_contact_form($useremail, $username, $token, $lang);
        echo json_encode($result);
    }
    
    public function send_number_confirm() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Gmail.php';
        $Gmail = new follows\cls\Gmail();

        $useremail = urldecode($_POST['useremail']);
        $username = urldecode($_POST['username']);
        $number = urldecode($_POST['number']);
        $lang = urldecode($_POST['lang']);

        $result = $Gmail->send_number_confirm($useremail, $username, $number, $lang);
        echo json_encode($result);
    }
    
    public function send_welcome() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Gmail.php';
        $Gmail = new follows\cls\Gmail();

        $useremail = urldecode($_POST['useremail']);
        $username = urldecode($_POST['username']);
        $lang = urldecode($_POST['lang']);

        $result = $Gmail->send_welcome($useremail, $username, $lang);
        echo json_encode($result);
    }
    
    public function send_client_cancel_status() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Gmail.php';
        $Gmail = new follows\cls\Gmail();

        $useremail = urldecode($_POST['useremail']);
        $username = urldecode($_POST['username']);
        $lang = urldecode($_POST['lang']);

        $result = $Gmail->send_client_cancel_status($useremail, $username, $lang);
        echo json_encode($result);
    }
    
    public function send_client_ticket_success() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new follows\cls\system_config();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/class/Gmail.php';
        $Gmail = new follows\cls\Gmail();

        $useremail = urldecode($_POST['useremail']);
        $username = urldecode($_POST['username']);
        $ticket_url = urldecode($_POST['ticket_url']);
        $lang = urldecode($_POST['lang']);

        $result = $Gmail->send_client_ticket_success($useremail, $username, $ticket_url, $lang);
        echo json_encode($result);
    }
    

}
