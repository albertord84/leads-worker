<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>User Contact</title>
</head>
<body>
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
  <h1>DUMBU LEADS - User Contact</h1>
  <p>The user <strong><?php echo $_GET["username"]; ?></strong> send us the following message:</p>
  <p style="font-style: italic; font-size: 13px;">"<?php echo $_GET["usermsg"]; ?>"</p>
  <br>
  <p>User personal contact information:</p>
  <p>email -> <strong><?php echo $_GET["useremail"]; ?></strong></p>
  <p>company -> <strong><?php echo $_GET["usercompany"]; ?></strong></p>
  <p>phone -> <strong><?php echo $_GET["userphone"]; ?></strong></p>
  <br>
  <p>DUMBU SYSTEM</p>
</div>
</body>
</html>
