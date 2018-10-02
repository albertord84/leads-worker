<?php

/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */

namespace leads\cls {
    //SMTP needs accurate times, and the PHP time zone MUST be set
    //This should be done in your php.ini, but this is how to do it if you don't have access to that
    date_default_timezone_set('Etc/UTC');

    //require_once 'libraries/PHPMailer-master/PHPMailerAutoload.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/leads/worker/externals/PHPMailer-master/PHPMailerAutoload.php';

    class Gmail {

        public $mail = NULL;

        public function __construct() {        
            $this->mail = new \PHPMailer; //Create a new PHPMailer instance
            $this->mail->isSMTP();  //Tell PHPMailer to use SMTP
            //
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $this->mail->SMTPDebug = 0;
            //$this->mail->SMTPDebug = 2;

            //Ask for HTML-friendly debug output
            $this->mail->Debugoutput = 'html';

            //Set the hostname of the mail server
            $this->mail->Host = 'smtp.gmail.com'; // dumbu.system
            //$this->mail->Host = 'imap.gmail.com'; // atendimento
            
            // use
            // $mail->Host = gethostbyname('smtp.gmail.com');
            // if your network does not support SMTP over IPv6
            //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
            $this->mail->Port = 587; // dumbu.system
            //$this->mail->Port = 993; // atendimento

            //Set the encryption system to use - ssl (deprecated) or tls
            $this->mail->SMTPSecure = 'tls'; // dumbu.system
            //$this->mail->SMTPSecure = 'ssl'; // atendimento

            //Whether to use SMTP authentication
            $this->mail->SMTPAuth = true; // dumbu.system
            //$this->mail->SMTPAuth = false; // atendimento

            //Username to use for SMTP authentication - use full email address for gmail
            //$this->mail->Username = $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN;
            //$this->mail->Username = $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN;
            //$this->mail->Username = $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN3;
            $this->mail->Username = $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN;

            //Password to use for SMTP authentication
            //$this->mail->Password = $GLOBALS['sistem_config']->SYSTEM_USER_PASS;
            //$this->mail->Password = $GLOBALS['sistem_config']->SYSTEM_USER_PASS2;
            //$this->mail->Password = $GLOBALS['sistem_config']->SYSTEM_USER_PASS3;
            $this->mail->Password = $GLOBALS['sistem_config']->SYSTEM_USER_PASS;

            //Set who the message is to be sent from
            //$this->mail->setFrom($GLOBALS['sistem_config']->SYSTEM_EMAIL, 'DUMBU');
            //$this->mail->setFrom($GLOBALS['sistem_config']->ATENDENT_EMAIL, 'DUMBU');
            //$this->mail->setFrom($GLOBALS['sistem_config']->SYSTEM_EMAIL3, 'DUMBU');
           
            $result = $this->mail->setFrom($GLOBALS['sistem_config']->SYSTEM_EMAIL, 'DUMBU');
        }
        
        
        public function send_client_contact_form($username, $useremail, $usermsg, $usercompany = NULL, $userphone = NULL) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to           
            $this->mail->clearAddresses();
            $this->mail->addAddress($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            $this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->clearReplyTos();
            $this->mail->addReplyTo($useremail, $username);
            $this->mail->isHTML(true);
            //Set the subject line
            $this->mail->Subject = "User Contact: $username";
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            $usermsg = urlencode($usermsg);
            $usercompany = urlencode($usercompany);
            $userphone = urlencode($userphone);
           
           // $this->mail->msgHTML(@file_get_contents("http://dumbu.one/follows/worker/resources/emails/contact_form.php?username=$username&useremail=$useremail&usercompany=$usercompany&userphone=$userphone&usermsg=$usermsg"), dirname(__FILE__));
            
            $this->mail->msgHTML(@file_get_contents("http://". $_SERVER['SERVER_NAME'] ."/leads/src/resources/emails/contact_form.php?username=$username&useremail=$useremail&usercompany=$usercompany&userphone=$userphone&usermsg=$usermsg"), dirname(__FILE__));
            //$this->mail->Body = $usermsg;
            //Replace the plain text body with one created manually
            $this->mail->AltBody = "User Contact: $username";
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            //-------------Alberto
            /* if (!$this->mail->send()) {
              echo "Mailer Error: " . $this->mail->ErrorInfo;
              } else {
              echo "Message sent!";
              }
              $this->mail->smtpClose(); */
            //-------------Jose R
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
            //-------------------
        }
        
        public function send_recovery_pass($useremail, $username, $token, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Recuperar senha';
                        
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Recover password';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Recuperar contrasena';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $token = urlencode($token);            
            $username = urlencode($username);            
            $lang = urlencode($lang);            
                
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/src/resources/$lang/emails/recover_pass.php?token=$token&username=$username"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Verificar conta';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //$this->mail->AddEmbeddedImage(realpath('../src/assets/img/confirm.png'), "logo_confirm", "confirm.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }
        
        public function send_number_confirm($useremail, $username, $number, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Verificar conta';
                        
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Check account';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Verificar cuenta';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
            $number = urlencode($number);            
                
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/src/resources/$lang/emails/number_confirm.php?username=$username&number=$number"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Verificar conta';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/confirm.png'), "logo_confirm", "confirm.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }
        
        public function send_welcome($useremail, $username, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Conta criada!';
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Created account!';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Cuenta creada!';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
                
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/src/resources/$lang/emails/welcome_message.php?username=$username"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Conta criada!';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/new_user.png'), "logo_new", "new_user.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }
        
        public function send_client_cancel_status($useremail, $username, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Conta cancelada';
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Account canceled!';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Cuenta cancelada!';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
                
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/src/resources/$lang/emails/cancel_status.php?username=$username"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Conta cancelada';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/email_cancel.png'), "logo_cancel", "email_cancel.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }
        
        public function send_client_ticket_success($useremail, $username, $ticket_url, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Boleto gerado com sucesso!';
            
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Ticket generated successfully!';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Boleto generado exitosamente!';
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
            $ticket_url = urlencode($ticket_url);            
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/src/resources/$lang/emails/ticket_success.php?username=$username&ticket_url=$ticket_url"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Boleto gerado com sucesso!';

            //Attach an image file
            //$this->mail->AddEmbeddedImage($_SERVER['SERVER_NAME'].'/leads/src/assets/img/bol.png', 'logo_boleto');
            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/email_bol.png'), "logo_boleto", "email_bol.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function send_mail($useremail, $username, $subject, $mail) {
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            $this->mail->Subject = $subject;

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            //$instaname = urlencode($instaname);
            //$instapass = urlencode($instapass);
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            $lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->Body = $mail;

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }

        public function send_client_login_error($useremail, $username, $instaname, $instapass = NULL) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            $this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Problemas no seu login';
            $this->mail->Subject = 'DUMBU Problem with your login';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            $instaname = urlencode($instaname);
            $instapass = urlencode($instapass);
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            $lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/dumbu/worker/resources/$lang/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->AltBody = 'DUMBU Problemas no seu login';
            $this->mail->AltBody = 'DUMBU Problem with your login';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }

        public function send_client_not_rps($useremail, $username, $instaname, $instapass) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            $this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Cliente sem perfis de referencia';
            $this->mail->Subject = 'DUMBU Client without reference profiles alert';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            $instaname = urlencode($instaname);
            $instapass = urlencode($instapass);
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            $lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/dumbu/worker/resources/$lang/emails/not_reference_profiles.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->AltBody = 'DUMBU Cliente sem perfis de referência';
            $this->mail->AltBody = 'DUMBU Client without reference profiles alert';

            //Attach an image file
            $mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }

        public function send_client_payment_error($useremail, $username, $instaname, $instapass, $diff_days = 0) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            $this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = "DUMBU Problemas de pagamento $diff_days dia(s)";
            $this->mail->Subject = "DUMBU Payment Issues $diff_days day(s)";

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            $instaname = urlencode($instaname);
            $instapass = urlencode($instapass);
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            $lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/dumbu/worker/resources/$lang/emails/payment_error.php?username=$username&instaname=$instaname&instapass=$instapass&diff_days=$diff_days"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->AltBody = 'DUMBU Problemas de pagamento';
            $this->mail->Subject = "DUMBU Payment Issues";

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
                //print "<b>Informações do erro:</b> " . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }

        public function send_client_payment_success($useremail, $username, $instaname, $instapass) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            $this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Sign in successfully approved!';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            $instaname = urlencode($instaname);
            $instapass = urlencode($instapass);
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            $lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/dumbu/worker/resources/$lang/emails/payment_success.php?username=$username&instaname=$instaname"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            $this->mail->Subject = 'DUMBU Sign in successfully approved!';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }

        

        public function send_new_client_payment_done($username, $useremail, $plane = 0) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            //$this->mail->addAddress($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            $this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->clearReplyTos();
            $this->mail->addReplyTo($useremail, $username);

            //Set the subject line
            $this->mail->Subject = 'New Client with payment!';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);
            $plane = urlencode($plane);
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            $lang = "PT";//$GLOBALS['sistem_config']->LANGUAGE;
            $email_msg = "http://" . $_SERVER['SERVER_NAME'] . "/dumbu/worker/resources/emails/new_client_with_payment.php?username=$username&useremail=$useremail";
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/dumbu/worker/resources/$lang/emails/new_client_with_payment.php?username=$username&useremail=$useremail&plane=$plane"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            $this->mail->AltBody = 'New Client with payment';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            //-------------Alberto
            /* if (!$this->mail->send()) {
              echo "Mailer Error: " . $this->mail->ErrorInfo;
              } else {
              echo "Message sent!";
              }
              $this->mail->smtpClose(); */
            //-------------Jose R
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
            //-------------------
        }
        
        public function send_client_pendent_status($useremail, $username, $day_to_block, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Pendente por pagamento!';
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Pending for payment!';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Pendiente de pago!';
            
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
            $day_to_block = urlencode($day_to_block);            
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/worker/resources/$lang/emails/pendent_status.php?username=$username&day_to_block=$day_to_block"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Pendente por pagamento!';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/email_pendent.png'), "logo_pendent", "email_pendent.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }      
                
        public function send_client_bloqued_status($useremail, $username, $lang) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Conta bloqueada';
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Account blocked!';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Cuenta bloqueada!';
            
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
                
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/worker/resources/$lang/emails/bloqued_status.php?username=$username"), dirname(__FILE__));

            //Replace the plain text body with one created manually
            //$this->mail->Subject = 'DUMBU Conta bloqueada';

            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/email_bloqued.png'), "logo_bloqued", "email_bloqued.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        } 
        
        public function send_client_response_cupom($useremail, $username, $lang, $brazilian, $value, $response) {
            //Set an alternative reply-to address
            //$mail->addReplyTo('albertord@ic.uff.br', 'First Last');
            //Set who the message is to be sent to
            $this->mail->clearAddresses();
            $this->mail->addAddress($useremail, $username);
            $this->mail->clearCCs();
            //$this->mail->addCC($GLOBALS['sistem_config']->SYSTEM_EMAIL, $GLOBALS['sistem_config']->SYSTEM_USER_LOGIN);
            //$this->mail->addCC($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);
            $this->mail->addReplyTo($GLOBALS['sistem_config']->ATENDENT_EMAIL, $GLOBALS['sistem_config']->ATENDENT_USER_LOGIN);

            //Set the subject line
            //$this->mail->Subject = 'DUMBU Assinatura aprovada com sucesso!';
            $this->mail->Subject = 'DUMBU Resposta à solicitude pré-pago';
            if($lang == "EN")
                $this->mail->Subject = 'DUMBU Prepaid request response';
            if($lang == "ES")
                $this->mail->Subject = 'DUMBU Respuesta a la solicitud prepago';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $username = urlencode($username);            
            $value = urlencode($value);            
            $brazilian = urlencode($brazilian);            
            $response = urlencode($response);            
            //$this->mail->msgHTML(file_get_contents("http://localhost/dumbu/worker/resources/emails/login_error.php?username=$username&instaname=$instaname&instapass=$instapass"), dirname(__FILE__));
            //echo "http://" . $_SERVER['SERVER_NAME'] . "<br><br>";
            //$lang = $GLOBALS['sistem_config']->LANGUAGE;
            $this->mail->msgHTML(@file_get_contents("http://" . $_SERVER['SERVER_NAME'] . "/leads/worker/resources/$lang/emails/cupom_response.php?username=$username&value=$value&brazilian=$brazilian&response=$response"), dirname(__FILE__));

            //Replace the plain text body with one created manually
//            $this->mail->Subject = 'DUMBU Boleto gerado com sucesso!';

            //Attach an image file
            //$this->mail->AddEmbeddedImage($_SERVER['SERVER_NAME'].'/leads/src/assets/img/bol.png', 'logo_boleto');
//            $this->mail->AddEmbeddedImage(realpath('../src/assets/img/email_bol.png'), "logo_boleto", "email_bol.png", "base64", "image/png");
            //send the message, check for errors
            if (!$this->mail->send()) {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $this->mail->ErrorInfo;
            } else {
                $result['success'] = true;
                $result['message'] = "Message sent!" . $this->mail->ErrorInfo;
            }
            $this->mail->smtpClose();
            return $result;
        }
        
        public function  sendAuthenticationErrorMail($username, $useremail){
            
        }
    }

}
