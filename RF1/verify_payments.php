<?php
// verify_payments.php (แก้: ประมวลผล POST ก่อน include header เพื่อให้ header() ทำงานได้)
require_once __DIR__ . '/functions.php';
require_login();
if (!is_treasurer()) { 
    // ยังไม่มี output ที่ถูกส่ง — ปลอดภัยที่จะ redirect
    flash('Access denied'); 
    header('Location: member_dashboard.php'); 
    exit; 
}

require_once __DIR__ . '/db.php';

// *** Handle POST first (ก่อน include header.php และก่อนมี output) ***
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { 
        flash('Invalid CSRF'); 
        header('Location: verify_payments.php'); 
        exit; 
    }
    $pid = (int)($_POST['payment_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE payment SET status='Paid', pay_date = NOW() WHERE payment_id=?");
        $stmt->execute([$pid]);
        flash('Payment approved');
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE payment SET status='Unpaid' WHERE payment_id=?");
        $stmt->execute([$pid]);
        flash('Payment rejected (Status set to Unpaid)');
    } elseif ($action === 'waive') {
        $stmt = $pdo->prepare("UPDATE payment SET status='Waived' WHERE payment_id=?");
        $stmt->execute([$pid]);
        flash('Payment waived');
    }
    // redirect หลังอัปเดต สำคัญ: ต้อง exit เพื่อหยุดการส่ง output เพิ่มเติม
    header('Location: verify_payments.php');
    exit;
}

// --- หลังจากจัดการ POST เสร็จแล้ว ค่อย include header และแสดงเพจ ---
include __DIR__ . '/header.php';

$stmt = $pdo->query("SELECT p.*, m.full_name, pp.month, pp.year 
                     FROM payment p 
                     JOIN member m ON p.member_id = m.member_id 
                     JOIN payment_period pp ON p.period_id = pp.period_id 
                     WHERE p.status='Pending' 
                     ORDER BY p.created_at ASC");
$rows = $stmt->fetchAll();
?>

<link rel="stylesheet" href="assets/verify_payments.css">

<h2>Verify Payments (รายการรอตรวจสอบ)</h2>
<p style="color:#666; margin-bottom: 15px;">
    ตรวจสอบรายการชำระเงินที่มีสถานะ 'Pending' เพื่ออนุมัติ (Paid), ปฏิเสธ (Unpaid), หรือยกเว้น (Waived).
</p>
<?php if (empty($rows)): ?>
    <p style='padding:15px; background:#f0f0f0; border-radius:8px; font-weight:600;'>No pending payments.</p>
<?php endif; ?>

<?php if (!empty($rows)): ?>
<form method="post" action="verify_payments.php">
    <input type="hidden" name="csrf" value="<?php echo csrf_token();?>">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Member</th>
                <th>Period</th>
                <th style="text-align:right;">Amount Paid</th>
                <th style="text-align:center;">Receipt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?php echo $r['payment_id'];?></td>
            <td>
                <strong><?php echo htmlspecialchars($r['full_name']);?></strong>
                <div style="font-size:0.8em; color:#999;"><?php echo $r['month'].'/'.$r['year'];?></div>
            </td>
            <td><?php echo $r['month'].'/'.$r['year'];?></td>
            <td style="text-align:right; font-weight: 500;"><?php echo number_format($r['amount_paid'], 2);?></td>
            <td style="text-align:center;">
                <?php if ($r['receipt_filename']): ?>
                    <a href="<?php echo htmlspecialchars($r['receipt_filename']);?>" target="_blank" class="receipt-link">View Receipt</a>
                <?php else: ?>
                    -
                <?php endif;?>
            </td>
            <td class="action-group">
                <button type="submit" name="action" value="approve" class="btn-approve">Approve (Paid)</button>
                <button type="submit" name="action" value="reject" class="btn-reject">Reject (Unpaid)</button>
                <button type="submit" name="action" value="waive" class="btn-waive">Waive</button>
                <input type="hidden" name="payment_id" value="<?php echo $r['payment_id'];?>">
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
