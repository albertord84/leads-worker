<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DUMBU Pendente por pagamento!</title>
	<style>
	div.red {
	    background-color: #ff3333;
	    color: white;
	}
	</style>
    </head>
    <body>
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <div class = "red" style="padding:24px 16px">
                <p style="text-align:center;">
                    <img src='cid:logo_pendent' style="vertical-align:middle">
                </p>
                <h1 style="text-align:center;">Pendente por pagamento!</h1>
            </div>
            <div ><p><h3>Olá <strong><?php echo $_GET["username"]; ?></strong>,</h3></p></div>
            <p> Você precisa atualizar seu pagamento para poder continuar extraindo leads!</p>
            <p> Em caso de não atualizar será bloqueado dentro <?php echo $_GET["day_to_block"]; ?> dias</p>
                        
            <p>Se tiver qualquer dúvida é só nos escrever!</p>
            <p>Obrigado por usar nossos serviços,</p>
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
