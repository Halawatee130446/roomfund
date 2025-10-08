<?php
// member_dashboard.php
require_once __DIR__ . '/functions.php';
require_login();
require_once __DIR__ . '/db.php';

// นำเข้า Sidebar สำหรับสมาชิก (ถ้าไม่ใช่ Treasurer)
if (!is_treasurer()) {
    include __DIR__ . '/member_sidebar.php';
}

$u = current_user();
$member_id = $u['member_id'];

// --- Overall totals (like treasurer dashboard but shown on member dashboard) ---
$stmt = $pdo->query("SELECT IFNULL(SUM(amount_paid),0) AS total_paid FROM payment WHERE status='Paid'");
$totalPaidRow = $stmt->fetch();
$total_paid_all = $totalPaidRow ? (float) $totalPaidRow['total_paid'] : 0.0;

$stmt = $pdo->query("SELECT IFNULL(SUM(amount),0) AS total_expenses FROM expense");
$totalExpRow = $stmt->fetch();
$total_expenses_all = $totalExpRow ? (float) $totalExpRow['total_expenses'] : 0.0;

$net_all = $total_paid_all - $total_expenses_all;

// --- Personal summary ---
$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM payment_period");
$periodRow = $stmt->fetch();
$total_periods = $periodRow ? (int) $periodRow['cnt'] : 0;

$stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM payment WHERE member_id = ? AND status = 'Paid'");
$stmt->execute([$member_id]);
$paidRow = $stmt->fetch();
$paid_count = $paidRow ? (int) $paidRow['cnt'] : 0;

// Count periods that the member has NOT paid (treat as not-paid if there's no 'Paid' record for that period)
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS cnt
    FROM payment_period p
    LEFT JOIN (
      SELECT period_id FROM payment WHERE member_id = ? AND status IN ('Paid','Pending','Waived')
    ) pr ON p.period_id = pr.period_id
    WHERE pr.period_id IS NULL
");
$stmt->execute([$member_id]);
$notPaidRow = $stmt->fetch();
$not_paid_count = $notPaidRow ? (int) $notPaidRow['cnt'] : 0; // นี่คือจำนวนงวดที่ยังไม่ได้ชำระหรือรอดำเนินการ

// Count payments that are Pending/Unpaid/Rejected (needs action)
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS cnt
    FROM payment_period pp
    LEFT JOIN payment p ON p.period_id = pp.period_id AND p.member_id = ?
    WHERE (p.status IS NULL OR p.status IN ('Unpaid', 'Pending')) AND pp.period_id IS NOT NULL
");
$stmt->execute([$member_id]);
$unpaid_or_pending_count = $stmt->fetchColumn();


// Count waived payments for this member (case-insensitive)
$stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM payment WHERE member_id = ? AND LOWER(status) IN ('waive','waived')");
$stmt->execute([$member_id]);
$waivedRow = $stmt->fetch();
$waived_count = $waivedRow ? (int) $waivedRow['cnt'] : 0;

include __DIR__ . '/header.php';
?>

<link rel="stylesheet" href="assets/member_dashboard.css">

<h2>Member Dashboard</h2>
<p style="font-size:1.1em; color:#555;">สวัสดี, "<?php echo htmlspecialchars($u['username']); ?>"!</p>

<!-- 1. Overall Net Summary Card (แสดงสถานะกองทุนโดยรวม) -->
<div style="max-width: 450px; margin-bottom: 30px;">
    <div class="info-card card-net">
        <div class="card-title">สถานะกองทุนโดยรวม (Net Balance)</div>
        <div class="card-value">
            <?php echo number_format($net_all, 2); ?> ฿
        </div>
        <div style="font-size: 0.8em; color: #555;">
            Paid (รวม): <?php echo number_format($total_paid_all, 2); ?> | Exp (รวม): <a href="list_expenses.php" style="color: #555;"><?php echo number_format($total_expenses_all, 2); ?></a>
        </div>
    </div>
</div>

<!-- 2. Personal Summary Cards (สถิติส่วนตัว) -->
<h3>ข้อมูลงวดการชำระ</h3>
<div class="member-stats-grid">
    
    <!-- A. Paid Count -->
    <div class="info-card card-paid-count">
        <div class="card-title">ชำระแล้ว</div>
        <div class="card-value"><?php echo $paid_count; ?> / <?php echo $total_periods; ?></div>
        <span class="card-footer-link" style="color:green;">Total Paids</span>
    </div>

    <!-- B. Unpaid/Pending Count -->
    <div class="info-card card-unpaid-count">
        <div class="card-title">ยังไม่ชำระ/รอการชำระ</div>
        <div class="card-value"><?php echo $unpaid_or_pending_count; ?></div>
        <?php if ($unpaid_or_pending_count > 0): ?>
             <a href="list_payments.php?member_id=<?php echo $member_id; ?>" style="color: #c62828;" class="card-footer-link">ดูรายการค้างชำระ</a>
        <?php else: ?>
             <span class="card-footer-link" style="color:#1a7c3b;">ไม่มีรายการค้างชำระ</span>
        <?php endif; ?>
    </div>

    <!-- C. Waived Count -->
    <div class="info-card card-waived">
        <div class="card-title">ได้รับการยกเว้น</div>
        <div class="card-value"><?php echo $waived_count; ?></div>
        <span class="card-footer-link" style="color:#673ab7;">Total Waived</span>
    </div>

    <!-- D. Total Periods -->
    <div class="info-card card-total-periods">
        <div class="card-title">รายการงวดทั้งหมด</div>
        <div class="card-value"><?php echo $total_periods; ?></div>
        <span class="card-footer-link" style="color:gray;">Total Periods</span>
    </div>

</div>

<!-- Quick links -->
<h3 style="margin-top: 30px;">เมนู</h3>
<div class="quick-link-group">
    <a href="list_periods.php">รายการงวดทั้งหมด</a>
    <a href="list_payments.php?member_id=<?php echo $member_id; ?>">ประวัติการชำระเงิน</a>
    <a href="edit_profile.php">แก้ไขข้อมูลส่วนตัว</a>
    <a href="list_expenses.php">ดูรายการค่าใช้จ่าย</a>
</div>

<?php include __DIR__ . '/footer.php'; ?>
