<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DUMBU Prepaid!</title>
	<style>
	div.red {
	    background-color: #00b359;
	    color: white;
	}
	</style>
    </head>
    <body>
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <div class = "red" style="padding:24px 16px">
<!--                <p style="text-align:center;">
                    <img src='cid:logo_bloqued' style="vertical-align:middle">
                </p>-->
                <h1 style="text-align:center;">Prepaid response</h1>
            </div>
            <div ><p><h3>Hi <strong><?php echo $_GET["username"]; ?></strong>,</h3></p></div>
            <?php
            $simb = "R$";
            if(!$_GET["brazilian"])
                $simb = "USD";
            if($_GET["response"] == 1){
            ?>
            <p> You buy a prepaid credit for <?php echo $simb." ".$_GET["value"]/100;?>.00. Create campaigns and start getting leads! </p>
            <?php
                }
                else{
            ?>
            <p> It was not possible to make the pre-payment of <?php echo $simb." ".$_GET["value"]/100;?>.00. Please, check the information on your card.</p>
            <?php
                }             
            ?>                                   
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
