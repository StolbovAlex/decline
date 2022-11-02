<?php
/*
 * logout.php
 * Сбрасывает переменные сессии, отвечающие за авторизацию пользователя
 */
   session_start();
   unset($_SESSION['UID']);
   header("Location: /index.php", true, 301);
?>
