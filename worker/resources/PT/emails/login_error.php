<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DUMBU Problemas no seu login</title>
    </head>
    <body>
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <h1>DUMBU Problemas no seu login.</h1>
            <!--  <div align="center">
                <a href="https://github.com/PHPMailer/PHPMailer/"><img src="images/phpmailer.png" height="90" width="340" alt="PHPMailer rocks"></a>
              </div>-->
            <p>Olá, <strong><?php echo $_GET["username"]; ?></strong>,</p>

            <p>Tivemos problemas com seu login! Caso você tenha trocado a sua senha do instagram siga as instruções no email. 
                Caso esteja todo certo não se preocupe por este email, as vezes o instagram pode gerar problemas para conectar alguma conta, 
                por isso mesmo esteja todo certo recomendamos fortemente fazer login no nosso sistema para eliminar as dúvidas.
            </p>
            <p>Lembre-se: você deve ter mesmo nome de usuário e senha para ambos, Instagram e nosso <a href="https://www.dumbu.pro/dumbu/src/">sistema</a>.
            <p>Você só precisa fazer o login no Dumbu com um nome de usuário e senha válidos e iguais ao seu Instagram.!</p>
            <p>Por favor, verifique suas credenciais de login no instagram e nosso <a href="https://www.dumbu.pro/dumbu/src/">sistema</a> 
                para continuar a ganhar seguidores rapidamente! :)</p>

            <br>
            <p>Seu nome de usuário Instagram no nosso sistema é: <strong><?php echo $_GET["instaname"]; ?></strong></p>
            <p>Sua senha é a mesma usada para você entrar no seu instagram.</p>

            <br>
            <p>Se tiver dúvidas, escreva para o e-mail atendimento@dumbu.pro</p>

            <br>
            <p>Obrigado por utilizar nossos serviços,</p>
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
