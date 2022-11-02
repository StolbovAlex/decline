<?php
/*
 * index.html
 * Приветствие пользователя и запрос авторизации
 */
    session_start();
    if (isset($_SESSION['UID'])) {
       header("Location: /logout.php", true, 301);
    }
?>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Вход</title>
</head>
<body topmargin=5 leftmargin=2 bgcolor="#dfdfb0"><TABLE width="760" align="center">

        <table width="300" height="100%" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
        <form name="login" method="post" action="login.php">
        <td>
        <fieldset>
        <table width="300" border="0" align="center" cellpadding="3" cellspacing="1">
        <tr>
        <td colspan="3"><strong>Вход </strong></td>
        </tr>
        <tr>
        <td width="78">Пользователь</td>
        <td width="6">:</td>
        <td width="294"><input name="myusername" type="text" id="myusername"></td>
        </tr>
        <tr>
        <td>Пароль</td>
        <td>:</td>
        <td><input name="mypassword" type="text" id="mypassword"></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Login"></td>
        </tr>
        </table>
        </fieldset>
        </td>
        </form>
        </tr>
        </table>
</body>
</html>
