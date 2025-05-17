<?php
session_start();
require_once __DIR__ . '/../config/db.php';


$_SESSION['user_id'] = $user['id'];
$_SESSION['is_admin'] = $user['is_admin'];

if ($_SESSION[$is_admin] == 1) {

} else {

}






?>