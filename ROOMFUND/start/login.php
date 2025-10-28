<?php
// login.php
require_once __DIR__ . '/../includes/config.php';
include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>../assets/login.css">
<div class="card">
  <h2>เข้าสู่ระบบ</h2>
  <?php if (!empty($error)) echo "<div class='flash'>".htmlspecialchars($error)."</div>"; ?>
  <form method="post" action="<?php echo BASE_URL; ?>/codebackend/submit_login.php">
    <label>ชื่อผู้ใช้</label>
    <input name="username" required>
    <label>รหัสผ่าน</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; // แก้ไขโดยลบ ../ ออก ?>