<?php
require_once __DIR__ . '/session.php';
$_SESSION = array();
session_destroy();
header('Location: ' . url_base() . '/auth/login.php');
exit;
