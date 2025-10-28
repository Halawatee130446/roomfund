<?php
// register.php
 require_once __DIR__ . '/../includes/init.php'; 
 
start_secure_session();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['full_name'] ?? '');
    $student_code = trim($_POST['student_code'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullname && $username && $password) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO member (full_name, student_code, tel, address, role) VALUES (?, ?, ?, ?, 'member')");
            $stmt->execute([$fullname, $student_code, $tel, $address]);
            $member_id = $pdo->lastInsertId();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO user_account (member_id, username, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$member_id, $username, $hash]);

            $pdo->commit();
            
            flash("Registered. You can log in now.");
            header('Location: ' . BASE_URL . ' /start/login.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($e->getCode() === '23000') {
                $error = "Username may already exist. Please choose another one.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    } else {
        $error = "Please fill all required fields (Full name, Username, Password).";
    }
}

include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>../assets/register.css">

<div id="content-wrapper"> 
    <div class="card">
        <h2>ลงทะเบียนสมาชิกใหม่</h2>
        <?php if (!empty($error)) echo "<div class='flash'>".htmlspecialchars($error)."</div>"; ?>
        <form method="post" action="<?php echo BASE_URL; ?>../start/register.php">
            
            <div class="form-grid">
                <div>
                    <label>ชื่อ-นามสกุล (จำเป็น)</label>
                    <input name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>

                <div>
                    <label>รหัสนักศึกษา</label>
                    <input name="student_code" value="<?php echo htmlspecialchars($_POST['student_code'] ?? ''); ?>">
                </div>
                
                <div>
                    <label>เบอร์โทร</label>
                    <input name="tel" value="<?php echo htmlspecialchars($_POST['tel'] ?? ''); ?>">
                </div>

                <div>
                    <label>ชื่อผู้ใช้ (จำเป็น)</label>
                    <input name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div>
                    <label>รหัสผ่าน (จำเป็น)</label>
                    <input type="password" name="password" required>
                </div>
            </div>
            
            <label>ที่อยู่</label>
            <textarea name="address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>

            <button type="submit">Register</button>
        </form>
    </div>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
