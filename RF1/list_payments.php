<?php
// list_payments.php (ปรับให้รองรับ ?member_id=... แสดงงวดที่ยังไม่ชำระ และเพิ่มคอลัมน์วันที่ชำระ)
require_once __DIR__ . '/functions.php';
require_login();
require_once __DIR__ . '/db.php';
if (!is_treasurer()) {
    include __DIR__ . '/member_sidebar.php';
}
include __DIR__ . '/header.php';

$u = current_user();
$isTreasurer = is_treasurer();

// รับ member_id จาก query (ถ้ามี)
$requested_member_id = isset($_GET['member_id']) ? (int)$_GET['member_id'] : null;

// ถ้าผู้ใช้ปกติ ส่ง member_id มาแต่ไม่ใช่ของตัวเอง ให้บังคับใช้ของตัวเองเท่านั้น (security)
if (!$isTreasurer) {
    // member ธรรมดา ต้องดูเฉพาะตัวเอง
    $member_id = $u['member_id'];
    $showAll = false;
} else {
    // ถ้าเป็น treasurer และไม่ได้ส่ง member_id => show all
    if ($requested_member_id) {
        $member_id = $requested_member_id;
        $showAll = false;
    } else {
        $member_id = null;
        $showAll = true;
    }
}

// Fetch data
if ($showAll) {
    // treasurer view: all payments (existing payment records)
    $stmt = $pdo->query("SELECT p.*, m.full_name, pp.month, pp.year 
                         FROM payment p 
                         JOIN member m ON p.member_id = m.member_id 
                         JOIN payment_period pp ON p.period_id = pp.period_id 
                         ORDER BY p.created_at DESC");
    $rows = $stmt->fetchAll();
    $heading = "All Payments (รายการชำระเงินทั้งหมด)";
} else {
    // show payments for specific member (may be treasurer viewing own or admin viewing specific member)
    // SELECT all payment_periods and LEFT JOIN payment for this member (include pay_date)
    $sql = "SELECT pp.period_id, pp.month, pp.year, pp.amount_due,
                    p.payment_id, p.amount_paid, p.status, p.receipt_filename, p.pay_date,
                    m.member_id, m.full_name
             FROM payment_period pp
             LEFT JOIN payment p ON p.period_id = pp.period_id AND p.member_id = ?
             JOIN member m ON m.member_id = ?  -- to get member name (single row)
             ORDER BY pp.year DESC, pp.month DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id, $member_id]);
    $rows = $stmt->fetchAll();

    // get member name for heading
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
<link rel="stylesheet" href="assets/list_payments.css">

<h2><?php echo $heading; ?></h2>

<?php if ($isTreasurer): ?>
    <p><a href='treasurer_dashboard.php' class="action-link" style="font-weight: 400;">← Back to Dashboard</a></p>
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

        // compute payable displays
        $status_class = 'status-unpaid';
        $status_label = 'Unpaid';
        $amount_paid_display = '-';
        $pay_date_display = '-';
        $action_html = '-';
        $is_waive = in_array($status_norm, ['waive', 'waived']);
        
        if (!$payment_id) {
            $action_html = "<a href='payment_form.php?period_id=" . urlencode($r['period_id'] ?? 0) . "' class='action-link'>Pay / Upload</a>";
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
            } else { // Rejected or explicit Unpaid record
                $status_label = 'Unpaid/Rejected';
                $status_class = 'status-unpaid';
                $action_html = "<a href='payment_form.php?period_id=" . urlencode($r['period_id'] ?? 0) . "' class='action-link'>Re-submit</a>";
            }
        }
        
        $receipt_link = ($is_waive || empty($receipt)) ? '-' : "<a href=\"" . htmlspecialchars($receipt) . "\" target=\"_blank\" class='receipt-link'>View</a>";

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

            <!-- Status cell with class -->
            <td style='text-align:center;'>
                <span class='status-badge <?php echo $status_class; ?>'>
                    <?php echo htmlspecialchars($status_label); ?>
                </span>
            </td>

            <!-- Receipt column -->
            <td style='text-align:center;'>
                <?php echo $receipt_link; ?>
            </td>

            <!-- Action column -->
            <td style='text-align:center;'>
                <?php echo $action_html; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

<?php include __DIR__ . '/footer.php'; ?>
