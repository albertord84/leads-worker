<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DUMBU Problemas de pagamento</title>
    </head>
    <body>
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <h1>DUMBU Problemas de pagamento.</h1>
            <!--  <div align="center">
                <a href="https://github.com/PHPMailer/PHPMailer/"><img src="images/phpmailer.png" height="90" width="340" alt="PHPMailer rocks"></a>
              </div>-->
            <p>Olá, <strong><?php echo $_GET["username"]; ?></strong>,</p>
            <p>Temos tido problemas em processar seu pagamento, verifique suas informações de cartão de crédito em nosso
                <a href="https://www.dumbu.pro/dumbu/src/">sistema</a>.</p>

            <br>
            <p>Seu nome de usuário Instagram no nosso sistema é: <strong><?php echo $_GET["instaname"]; ?></strong></p>
            <p>Sua senha é a mesma usada para você entrar no seu instagram.</p>

            <br>
            <?php
            require_once '../../../class/system_config.php';
            $diff_days = $_GET["diff_days"];
            if ($diff_days <= 0) {
                echo "<p> A sua conta foi bloqueada por pagamento! </p>";
            } else {
                echo "<p> A sua conta será bloqueada por pagamento em '$diff_days' dia(s)! </p>";
            }
            ?>

            <br>
            <p>Se tiver dúvidas, escreva para o e-mail atendimento@dumbu.pro</p>

            <br>
            <p>Obrigado por utilizar nossos serviços,</p>
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
