<?php
// запуск сесии 
session_start();
// удаляет все переменные из текущей сессии.
session_unset();
// полностью уничтожает сессию, включая сессионный ID.
session_destroy();

header('Location: /');
exit;

?>