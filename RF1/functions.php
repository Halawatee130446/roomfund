<?php
// functions.php
require_once 'config.php';

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        // แนะนำให้ตั้งค่า session params เพิ่มเติมใน production
    }
}

function is_logged_in() {
    start_secure_session();
    return !empty($_SESSION['user']);
}

function require_login() {
    start_secure_session();
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user() {
    start_secure_session();
    return $_SESSION['user'] ?? null;
}

function is_treasurer() {
    $u = current_user();
    return $u && ($u['role'] === 'treasurer');
}

function flash($msg=null) {
    start_secure_session();
    if ($msg === null) {
        if (!empty($_SESSION['flash'])) {
            $m = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $m;
        }
        return null;
    } else {
        $_SESSION['flash'] = $msg;
    }
}

function csrf_token() {
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    start_secure_session();
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}

function safe_upload($file, $prefix = 'receipt_') {
    // ตรวจสอบชนิดไฟล์และขนาดเบื้องต้น
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return [ 'error' => 'No file uploaded' ];
    if ($file['size'] > 5 * 1024 * 1024) return [ 'error' => 'File too large (>5MB)' ];
    $allowed = ['image/png','image/jpeg','application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed)) return ['error' => 'Invalid file type'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest = UPLOAD_DIR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return ['error' => 'Unable to move file'];
    return ['success' => true, 'filename' => $filename];
}

/**
 * เขียนไฟล์ .php ที่คืนค่า array แบบปลอดภัย (overwrites)
 * $path: path ของไฟล์ เช่น __DIR__ . '/payment_channels.php'
 * $data: array ที่ต้องการเก็บ
 * คืนค่า true/false
 */
function write_php_array_file($path, $data) {
    // สร้างเนื้อหา PHP ที่คืนค่า array
    $export = var_export($data, true);
    $content = "<?php\n// auto-generated\nreturn " . $export . ";\n";
    $tmpfile = $path . '.tmp';
    if (file_put_contents($tmpfile, $content, LOCK_EX) === false) return false;
    if (!rename($tmpfile, $path)) {
        @unlink($tmpfile);
        return false;
    }
    return true;
}
