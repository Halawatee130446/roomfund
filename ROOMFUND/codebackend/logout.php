<?php
// logout.php
 require_once __DIR__ . '/../includes/init.php'; 
start_secure_session();
session_unset();
session_destroy();
header('Location: ' . BASE_URL . ' /start/login.php');
exit;
