<?php
// submit_payment.php
require_once 'functions.php';
require_login();
require_once 'db.php';
$u = current_user();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: member_dashboard.php'); exit;
}
if (!verify_csrf($_POST['csrf'] ?? '')) { flash('Invalid CSRF'); header('Location: member_dashboard.php'); exit; }
$period_id = (int)($_POST['period_id'] ?? 0);
$amount = (float)($_POST['amount_paid'] ?? 0);
$result = safe_upload($_FILES['receipt'] ?? null);
if (!empty($result['error'])) {
    flash('Upload error: '.$result['error']);
    header('Location: payment_form.php?period_id=' . $period_id); exit;
}
$filename = $result['filename'];
// insert or update payment (if exists)
$stmt = $pdo->prepare("SELECT payment_id FROM payment WHERE member_id=? AND period_id=?");
$stmt->execute([$u['member_id'],$period_id]);
$exists = $stmt->fetch();
if ($exists) {
    $stmt = $pdo->prepare("UPDATE payment SET amount_paid=?, pay_date=NOW(), receipt_filename=?, status='Pending' WHERE payment_id=?");
    $stmt->execute([$amount, $filename, $exists['payment_id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO payment (member_id, period_id, amount_paid, pay_date, receipt_filename, status) VALUES (?, ?, ?, NOW(), ?, 'Pending')");
    $stmt->execute([$u['member_id'],$period_id,$amount,$filename]);
}
flash('Payment submitted (Pending verification)');
header('Location: member_dashboard.php');
exit;
