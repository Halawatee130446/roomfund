<?php
// logout.php
require_once 'functions.php';
start_secure_session();
session_unset();
session_destroy();
header('Location: login.php');
exit;
