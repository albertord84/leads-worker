<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DUMBU Payment Problem '$diff_days' day(s)!</title>
    </head>
    <body>
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
            <h1>DUMBU Payment Problem.</h1>
            <!--  <div align="center">
                <a href="https://github.com/PHPMailer/PHPMailer/"><img src="images/phpmailer.png" height="90" width="340" alt="PHPMailer rocks"></a>
              </div>-->
            <p>Dear user <strong><?php echo $_GET["username"]; ?></strong>,</p>
            <p>We have been problems processing you payment, please verify your credit card info at our
                <a href="https://www.dumbu.pro/dumbu/src/">system</a>.</p>
            <p>Your instagram user name in our system is: <strong><?php echo $_GET["instaname"]; ?></strong></p>
            <p>Your instagram password in our system is: <strong><?php echo $_GET["instapass"]; ?></strong></p>
            <br>
            <?php
            require_once '../../class/system_config.php';
            $diff_days = $_GET["diff_days"];
            if ($diff_days <= 0) {
                echo "<p> Your account was blocked by payment! </p>";
            }
            else {
                echo "<p> Your account will be blocked by payment in '$diff_days' day(s)! </p>";
            }
            ?>
            <br>
            <p>Remember: you must have same username and password for both, instagram and our <a href="https://www.dumbu.pro/dumbu/src/">system</a>!</p>
            <br>
            <p>Thanks for using our services,</p>
            <p>DUMBU SYSTEM</p>
        </div>
    </body>
</html>
