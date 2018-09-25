<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Boleto gerado com sucesso!</title>
	<style>
	div.green {
	    background-color: #00b359;
	    color: white;
	}
	</style>
    </head>
    <body>
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <div class = "green" style="padding:24px 16px">
                <p style="text-align:center;">
                    <img src='cid:logo_boleto'  style="vertical-align:middle">
                </p>
                
                <h1 style="text-align:center;">Boleto gerado com sucesso! </h1>
                
            </div>
            <div ><p><h3>Olá <strong><?php echo $_GET["username"]; ?></strong>,</h3></p></div>
            <p> Seu boleto foi gerado com sucesso!</p>
            <p> Baixe o boleto bancário clicando
                <a href="<?php echo $_GET["ticket_url"]; ?>">AQUI</a>,
                e efetue o pagamento quanto antes para poder obter seus leads!
            </p>
            
            <p>Obrigado por usar nossos serviços,</p>
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
