<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DUMBU Pendiente de pago!</title>
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
                <h1 style="text-align:center;">Pendiente de pago!</h1>
            </div>
            <div ><p><h3>Hi <strong><?php echo $_GET["username"]; ?></strong>,</h3></p></div>
            <p> Debe actualizar su pago para continuar extrayendo leads!</p>
            <p> En caso de que no actualice su pago, será bloqueado en <?php echo $_GET["day_to_block"]; ?> días</p>
                        
            <p>Si tiene alguna duda, ¡solo escríbanos!</p>
            <p>Gracias por usar nuestros servicios,</p>
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
