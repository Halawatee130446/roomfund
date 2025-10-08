<?php
// edit_profile.php
require_once __DIR__ . '/functions.php';
require_login();
require_once __DIR__ . '/db.php';

$u = current_user();
$member_id = $u['member_id'];

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $err = 'Invalid CSRF';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $student_code = trim($_POST['student_code'] ?? '');
        $tel = trim($_POST['tel'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $stmt = $pdo->prepare("UPDATE member SET full_name = ?, student_code = ?, tel = ?, address = ? WHERE member_id = ?");
        $stmt->execute([$full_name, $student_code, $tel, $address, $member_id]);

        flash('Profile updated');
        header('Location: member_dashboard.php');
        exit;
    }
}

// fetch current data
$stmt = $pdo->prepare("SELECT * FROM member WHERE member_id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch();

if (!is_treasurer()) {
    include __DIR__ . '/member_sidebar.php';
}

include __DIR__ . '/header.php';
?>
<link rel="stylesheet" href="assets/edit_profile.css">
<div class="form-card">
    <h2 style="text-align:center;">Edit My Profile</h2>

    <?php if ($err) echo "<div class='flash-error'>".htmlspecialchars($err)."</div>"; ?>

    <form method="post" action="edit_profile.php" class="styled-form">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        
        <label for="full_name">Full name:</label>
        <input name="full_name" id="full_name" required value="<?php echo htmlspecialchars($member['full_name'] ?? ''); ?>">
        
        <label for="student_code">Student code:</label>
        <input name="student_code" id="student_code" value="<?php echo htmlspecialchars($member['student_code'] ?? ''); ?>">
        
        <label for="tel">Tel:</label>
        <input name="tel" id="tel" value="<?php echo htmlspecialchars($member['tel'] ?? ''); ?>">
        
        <label for="address">Address:</label>
        <textarea name="address" id="address"><?php echo htmlspecialchars($member['address'] ?? ''); ?></textarea>
        
        <button type="submit">Save Profile</button>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
