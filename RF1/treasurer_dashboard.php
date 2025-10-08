<?php
// treasurer_dashboard.php (ปรับให้แสดง Total Paid เป็นตัวเลข และลิงก์ไปดู per-period)
require_once __DIR__ . '/functions.php';
require_login();
if (!is_treasurer()) { flash('Access denied'); header('Location: member_dashboard.php'); exit; }
require_once __DIR__ . '/db.php';

// Total Paid (รวมยอดที่ status='Paid')
$stmt = $pdo->query("SELECT IFNULL(SUM(amount_paid),0) AS total_paid FROM payment WHERE status='Paid'");
$totalPaidRow = $stmt->fetch();
$total_paid = $totalPaidRow ? (float)$totalPaidRow['total_paid'] : 0.0;

// Total Expenses (จะใช้ต่อ)
$stmt = $pdo->query("SELECT IFNULL(SUM(amount),0) AS total_expenses FROM expense");
$totalExpRow = $stmt->fetch();
$total_expenses = $totalExpRow ? (float)$totalExpRow['total_expenses'] : 0.0;

$net_balance = $total_paid - $total_expenses;

// Total members (รวมแอดมินด้วย)
$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM member");
$membersRow = $stmt->fetch();
$total_members = $membersRow ? (int)$membersRow['cnt'] : 0;

// total periods
$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM payment_period");
$periodRow = $stmt->fetch();
$total_periods = $periodRow ? (int)$periodRow['cnt'] : 0;

// pending verification count
$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM payment WHERE status='Pending'");
$pendRow = $stmt->fetch();
$pending_count = $pendRow ? (int)$pendRow['cnt'] : 0;

// periods list for selector
$periods = $pdo->query("SELECT period_id, month, year FROM payment_period ORDER BY year DESC, month DESC")->fetchAll();

include __DIR__ . '/header.php';
?>

<link rel="stylesheet" href="assets/treasurer_dashboard.css">   

<h2>Treasurer Dashboard</h2>

<!-- Dashboard Summary Cards -->
<div class="dashboard-grid">
    
    <!-- 1. Net Balance (Primary/Accent Card) -->
    <div class="info-card card-net" style="grid-column: span 1;">
        <div class="card-title" style="color: #07942aff;">Net Balance</div>
        <div class="card-value">
            <?php echo number_format($net_balance, 2); ?>
        </div>
        <div style="font-size: 0.8em; color: #555;">
            Paid: <?php echo number_format($total_paid, 2); ?> | Exp: <a href="list_expenses.php" style="color: #555;"><?php echo number_format($total_expenses, 2); ?></a>
        </div>
    </div>
    
    <!-- 2. Total Paid -->
    <div class="info-card card-paid">
        <div class="card-title" >Total Paid</div>
        <div class="card-value" style="color:#003ebaff; ;"><?php echo number_format($total_paid, 2); ?></div>
        <a href="period_payments.php" class="card-footer-link">View by Period</a>
    </div>

    <!-- 3. Pending Verification -->
    <div class="info-card card-pending">
        <div class="card-title" style="color: #6d0d0dff">Pending Verification</div>
        <div class="card-value"><?php echo $pending_count; ?></div>
        <?php if ($pending_count > 0): ?>
            <a href="verify_payments.php" class="card-footer-link" style="color: #6d0d0dff;">Verify Now</a>
        <?php else: ?>
            <span class="card-footer-link" style="color: #666; font-size: 0.8em;">No Pending Payments</span>
        <?php endif; ?>
    </div>

    <!-- 4. Total Expenses -->
    <div class="info-card card-expense">
        <div class="card-title" style="color:#a15518ff;">Total Expenses</div>
        <div class="card-value"><?php echo number_format($total_expenses, 2); ?></div>
        <a href="list_expenses.php" style="color: #a15518ff;" class="card-footer-link">View Expenses</a>
    </div>

    <!-- 5. Total Members -->
    <div class="info-card card-member">
        <div class="card-title" style="color:#673ab7;">Total Members</div>
        <div class="card-value"><?php echo $total_members; ?></div>
        <a href="members_list.php" style="color: #673ab7;" class="card-footer-link">View Members</a>
    </div>

    <!-- 6. Total Periods -->
    <div class="info-card card-period">
        <div class="card-title">Total Periods</div>
        <div class="card-value"><?php echo $total_periods; ?></div>
        <a href="list_periods.php" class="card-footer-link">View Periods</a>
    </div>

</div>

<!-- Period Selector and Action Links -->
<div style="border-top: 1px solid #eee; padding-top: 20px;">
    <!-- small period selector: ถ้าต้องการเลือกงวดแล้วไปดูเฉพาะงวด -->
    <form method="get" action="period_payments.php" style="margin-bottom:15px; display:flex; align-items:center; gap:10px;">
        <label style="font-weight: bold; color: #555;">เลือกงวดเพื่อดูสถานะ:</label>
        <select name="period_id" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #ccc; background: #fff;">
            <option value="">------ ล่าสุด ------</option>
            <?php foreach($periods as $p): ?>
                <option value="<?php echo $p['period_id']; ?>">เดือน <?php echo htmlspecialchars($p['month']); ?> / <?php echo htmlspecialchars($p['year']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="padding: 8px 15px; border-radius: 8px; background-color: var(--primary-pastel, #b4e1ff); border: none; font-weight: 600; cursor: pointer;">View</button>
    </form>

<?php include __DIR__ . '/footer.php'; ?>
