<?php
// list_periods.php (ปรับ: เพิ่ม Action สำหรับสมาชิก แสดง Paid / Pending / Unpaid / Waived)
require_once __DIR__ . '/functions.php';
require_login();
require_once __DIR__ . '/db.php';
if (!is_treasurer()) {
    include __DIR__ . '/member_sidebar.php';
}
include __DIR__ . '/header.php';

$u = current_user();
$isTreasurer = is_treasurer();

// ดึงงวดทั้งหมด
$stmt = $pdo->query("SELECT * FROM payment_period ORDER BY year DESC, month DESC");
$rows = $stmt->fetchAll();
?>

<link rel="stylesheet" href="assets/list_periods.css">

<h2>All Payment Periods (รายการงวดทั้งหมด)</h2>
<?php if ($isTreasurer): ?>
    <p style="margin-bottom: 20px;">
        <a href="create_period.php" class="action-button action-primary" style="padding: 10px 20px;">+ Create New Period</a>
    </p>
<?php endif; ?>

<table class="styled-table">
<thead>
    <tr>
        <th>ID</th>
        <th>Month/Year</th>
        <th style="text-align:right;">Amount (฿)</th>
        <th>Due Date</th>
        <?php if ($isTreasurer): ?>
            <th>Actions</th>
        <?php else: ?>
            <th>Your Status / Action</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($rows as $r): 
    $period_id = (int)$r['period_id'];
    $amount_due = number_format($r['amount_due'], 2);
    $due_date = htmlspecialchars($r['due_date']);
?>
<tr>
    <td><?php echo $r['period_id'];?></td>
    <td><?php echo $r['month'];?> / <?php echo $r['year'];?></td>
    <td style="text-align:right; font-weight: 500;"><?php echo $amount_due;?></td>
    <td><?php echo $due_date;?></td>

    <?php if ($isTreasurer): ?>
        <td>
            <a href="delete_period.php?id=<?php echo $period_id;?>" 
               class="action-button action-danger" 
               title="Delete Period (This will also delete all related payments)">
                Delete
            </a>
            <a href="period_payments.php?period_id=<?php echo $period_id; ?>" class="action-button action-secondary">View Status</a>
        </td>
    <?php else: 
        // Logic สำหรับสมาชิก
        $stmt2 = $pdo->prepare("SELECT payment_id, amount_paid, status, pay_date FROM payment WHERE member_id = ? AND period_id = ? LIMIT 1");
        $stmt2->execute([$u['member_id'], $period_id]);
        $pay = $stmt2->fetch();

        $status_class = 'status-unpaid';
        $status_label = 'Unpaid';
        $action_html = "<a href='payment_form.php?period_id={$period_id}' class='action-button action-primary'>Pay / Upload</a>";

        if ($pay) {
            $s = $pay['status'];
            if ($s === 'Paid') {
                $status_class = 'status-paid';
                $status_label = 'Paid';
                $pd = $pay['pay_date'] ? date('Y-m-d H:i', strtotime($pay['pay_date'])) : '';
                $action_html = $pd ? "<small style='color:#555;'>Paid on " . htmlspecialchars($pd) . "</small>" : "";
            } elseif ($s === 'Pending') {
                $status_class = 'status-pending';
                $status_label = 'Pending';
                $action_html = "<a href='list_payments.php?member_id={$u['member_id']}' class='action-button action-secondary'>View Submission</a>";
            } elseif ($s === 'Waived') {
                $status_class = 'status-waived';
                $status_label = 'Waived';
                $action_html = "<small style='color:#888;'>ยกเว้นการชำระ</small>";
            } else { // 'Unpaid' or Rejected
                 $status_class = 'status-unpaid';
                 $status_label = 'Rejected/Unpaid';
                 $action_html = "<a href='payment_form.php?period_id={$period_id}' class='action-button action-primary'>Re-submit</a>";
            }
        }
    ?>
        <td>
            <span class="status-badge <?php echo $status_class; ?>">
                <?php echo htmlspecialchars($status_label); ?>
            </span>
            <br/>
            <?php echo $action_html; ?>
        </td>
    <?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php include __DIR__ . '/footer.php'; ?>
