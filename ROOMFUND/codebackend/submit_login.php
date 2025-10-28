<?php
// submit_login.php
 require_once __DIR__ . '/../includes/init.php'; 
start_secure_session();

if (is_logged_in()) {
    if ($_SESSION['user']['role'] === 'treasurer') {
        header('Location: ' . BASE_URL . ' /views/treasurer_dashboard.php');
    } else {
        header('Location: ' . BASE_URL . ' /views/member_dashboard.php');
    }
    exit;
}

 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT ua.user_id, ua.username, ua.password_hash, m.member_id, m.role
        FROM user_account ua
        JOIN member m ON ua.member_id = m.member_id
        WHERE ua.username = ?
    ");
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'user_id' => (int)$row['user_id'],
            'member_id' => (int)$row['member_id'],
            'username' => $row['username'],
            'role' => $row['role'],
        ];

        if ($row['role'] === 'treasurer') {
            header('Location: ' . BASE_URL . ' /views/treasurer_dashboard.php');
        } else {
            header('Location: ' . BASE_URL . ' /views/member_dashboard.php');
        }
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
header('Location: ' . BASE_URL . ' /start/login.php?error=' . urlencode($error));
exit;