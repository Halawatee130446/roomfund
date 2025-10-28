<?php
// payment_form.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
 

$u = current_user();
$period_id = (int)($_GET['period_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM payment_period WHERE period_id = ?");
$stmt->execute([$period_id]);
$period = $stmt->fetch();
if (!$period) { flash('Period not found'); header('Location: ' . BASE_URL . ' /views/member_dashboard.php'); exit; }

$channels_file = ROOT_PATH . '/codebackend/payment_channels.php';
$payment_channels = [];
if (file_exists($channels_file)) {
    $payment_channels = include $channels_file;
    if (!is_array($payment_channels)) $payment_channels = [];
}

if (!is_treasurer()) {
    include ROOT_PATH . '/includes/member_sidebar.php';
}

include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="../assets/payment_form.css">
<div class="payment-container">
    <h2>💵 Submit Payment for Month <?php echo $period['month'];?> / <?php echo $period['year'];?></h2>
    <?php if (!empty($payment_channels)): ?>
        <h3>ช่องทางการชำระ (โปรดโอนตามข้อมูลด้านล่าง)</h3>
        <ul class="channel-list">
        <?php foreach($payment_channels as $ch): ?>
            <li>
                <b><?php echo htmlspecialchars($ch['bank']); ?></b>
                — <?php echo htmlspecialchars($ch['account_no']); ?>,
                <?php echo htmlspecialchars($ch['account_name']); ?>
                <?php if (!empty($ch['note'])): ?> <br/><small>Note: <?php echo htmlspecialchars($ch['note']); ?></small><?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="flash" style="background:#ffdddd; color:#cc0000; padding:10px; border-radius:8px;">
            <em>⚠️ ยังไม่มีข้อมูลช่องทางการชำระ กรุณาติดต่อเหรัญญิก</em>
        </p>
    <?php endif; ?>

    <div class="current-date">
        วันที่ชำระ: <?php echo date('Y-m-d'); ?>
    </div>

    <form method="post" action="<?php echo BASE_URL; ?>/codebackend/submit_payment.php" enctype="multipart/form-data" class="payment-form">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="period_id" value="<?php echo $period_id; ?>">
        <label for="amount_paid">จำนวนเงินที่ชำระ (฿)</label>
        <input name="amount_paid" id="amount_paid" type="number" step="0.01" required value="<?php echo htmlspecialchars($period['amount_due']); ?>">
        <label for="receipt">อัปโหลดสลิป (jpg/png/pdf)</label>
        <input type="file" name="receipt" id="receipt" required>
        <button type="submit">Submit Payment</button>
    </form>
</div>
<?php include ROOT_PATH . '/includes/footer.php'; ?>
