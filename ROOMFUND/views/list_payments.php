<?php
// list_payments.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
 
if (!is_treasurer()) {
    include ROOT_PATH . '/includes/member_sidebar.php';
}
include ROOT_PATH . '/includes/header.php';

$u = current_user();
$isTreasurer = is_treasurer();

$requested_member_id = isset($_GET['member_id']) ? (int)$_GET['member_id'] : null;

if (!$isTreasurer) {
    $member_id = $u['member_id'];
    $showAll = false;
} else {
    if ($requested_member_id) {
        $member_id = $requested_member_id;
        $showAll = false;
    } else {
        $member_id = null;
        $showAll = true;
    }
}

if ($showAll) {
    $stmt = $pdo->query("SELECT p.*, m.full_name, pp.month, pp.year 
                         FROM payment p 
                         JOIN member m ON p.member_id = m.member_id 
                         JOIN payment_period pp ON p.period_id = pp.period_id 
                         ORDER BY p.created_at DESC");
    $rows = $stmt->fetchAll();
    $heading = "All Payments (รายการชำระเงินทั้งหมด)";
} else {
    $sql = "SELECT pp.period_id, pp.month, pp.year, pp.amount_due,
                    p.payment_id, p.amount_paid, p.status, p.receipt_filename, p.pay_date,
                    m.member_id, m.full_name
             FROM payment_period pp
             LEFT JOIN payment p ON p.period_id = pp.period_id AND p.member_id = ?
             JOIN member m ON m.member_id = ?
             ORDER BY pp.year DESC, pp.month DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id, $member_id]);
    $rows = $stmt->fetchAll();

    $memberName = 'Member';
    if (!empty($rows)) {
        $memberName = $rows[0]['full_name'] ?? $memberName;
    } else {
        $stmt2 = $pdo->prepare("SELECT full_name FROM member WHERE member_id = ?");
        $stmt2->execute([$member_id]);
        $memberInfo = $stmt2->fetch();
        if ($memberInfo) $memberName = $memberInfo['full_name'];
    }
    $heading = "Payments for: " . htmlspecialchars($memberName) . " (สถานะการชำระเงิน)";
}
?>
<link rel="stylesheet" href="../assets/list_payments.css">

<h2><?php echo $heading; ?></h2>

<?php if ($isTreasurer): ?>
    <p><a href='../views/treasurer_dashboard.php' class="action-link" style="font-weight: 400;">← Back to Dashboard</a></p>
<?php endif; ?>


<table class='styled-table'>
<thead>
    <tr>
        <th>#</th>
        <?php if ($showAll): ?><th>Member</th><?php endif; ?>
        <th>Period (M/Y)</th>
        <th style='text-align:right;'>Amount Due (฿)</th>
        <th style='text-align:right;'>Amount Paid (฿)</th>
        <th style='text-align:center;'>Pay Date</th>
        <th style='text-align:center;'>Status</th>
        <th style='text-align:center;'>Receipt</th>
        <th style='text-align:center;'>Action</th>
    </tr>
</thead>
<tbody>

<?php if (empty($rows)): ?>
    <tr><td colspan='<?php echo $showAll ? 9 : 8; ?>' style='text-align:center; padding: 20px;'>No payments found.</td></tr>
<?php else: ?>
    <?php 
    $i = 0;
    foreach ($rows as $r):
        $i++;
        $memberDisplay = htmlspecialchars($r['full_name'] ?? ($u['username'] ?? 'Member'));
        $periodDisplay = (isset($r['month']) ? $r['month'] : '-') . '/' . (isset($r['year']) ? $r['year'] : '-');
        $amount_due = isset($r['amount_due']) ? number_format((float)$r['amount_due'],2) : '-';

        $payment_id = $r['payment_id'] ?? null;
        $amount_paid = $r['amount_paid'] ?? null;
        $status_raw = $r['status'] ?? ''; 
        $status_norm = strtolower(trim((string)$status_raw));
        $receipt = $r['receipt_filename'] ?? '';
        $pay_date_raw = $r['pay_date'] ?? null;

        $status_class = 'status-unpaid';
        $status_label = 'Unpaid';
        $amount_paid_display = '-';
        $pay_date_display = '-';
        $action_html = '-';
        $is_waive = in_array($status_norm, ['waive', 'waived']);
        
        if (!$payment_id) {
            $action_html = "<a href='../forms/payment_form.php?period_id=" . urlencode($r['period_id'] ?? 0) . "' class='action-link'>Pay / Upload</a>";
        } else {
            $amount_paid_display = number_format((float)$amount_paid,2);
            $ts = !empty($pay_date_raw) && $pay_date_raw !== '0000-00-00 00:00:00' ? strtotime($pay_date_raw) : false;
            $pay_date_display = $ts ? date('Y-m-d H:i', $ts) : '-';

            if ($is_waive) {
                $status_label = 'Waived';
                $status_class = 'status-waive';
                $action_html = 'Exempted';
            } elseif ($status_norm === 'paid') {
                $status_label = 'Paid';
                $status_class = 'status-paid';
            } elseif ($status_norm === 'pending') {
                $status_label = 'Pending';
                $status_class = 'status-pending';
                if ($isTreasurer) {
                    $action_html = "<a href='verify_payments.php' class='action-link'>Verify</a>";
                } else {
                    $action_html = "<span style='color:#999; font-size:0.9em;'>Waiting Approval</span>";
                }
            } else {
                $status_label = 'Unpaid/Rejected';
                $status_class = 'status-unpaid';
                $action_html = "<a href='../forms/payment_form.php?period_id=" . urlencode($r['period_id'] ?? 0) . "' class='action-link'>Re-submit</a>";
            }
        }
        
        if ($is_waive || empty($receipt)) {
            $receipt_link = '-';
        } else {
            if (strpos($receipt, '/') === false) {
                $url = '../uploads/' . $receipt;
            } else {
                $url = $receipt;
            }
            $receipt_link = "<a href=\"" . htmlspecialchars($url) . "\" target=\"_blank\" class='receipt-link'>View</a>";
        }

    ?>
        <tr>
            <td><?php echo $i; ?></td>
            <?php if ($showAll): ?>
                <td><?php echo $memberDisplay; ?></td>
            <?php endif; ?>
            <td><?php echo $periodDisplay; ?></td>
            <td style='text-align:right; font-weight: 500;'><?php echo $amount_due; ?></td>
            <td style='text-align:right; font-weight: 500;'><?php echo $amount_paid_display; ?></td>
            <td style='text-align:center; font-size: 0.9em; color:#777;'><?php echo $pay_date_display; ?></td>

            <td style='text-align:center;'>
                <span class='status-badge <?php echo $status_class; ?>'>
                    <?php echo htmlspecialchars($status_label); ?>
                </span>
            </td>

            <td style='text-align:center;'>
                <?php echo $receipt_link; ?>
            </td>

            <td style='text-align:center;'>
                <?php echo $action_html; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
