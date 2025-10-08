<?php
// members_list.php (ปรับปรุง UI ให้เป็นตารางที่มีสไตล์)
require_once __DIR__ . '/functions.php';
require_login();
if (!is_treasurer()) { flash('Access denied'); header('Location: member_dashboard.php'); exit; }
require_once __DIR__ . '/db.php';
include __DIR__ . '/header.php';

// ดึงสมาชิกทั้งหมด (รวม treasurer)
// จากเดิม: $stmt = $pdo->query("SELECT member_id, full_name FROM member WHERE role <> 'treasurer' ORDER BY full_name");
$stmt = $pdo->query("SELECT member_id, full_name, role FROM member ORDER BY full_name"); 
$members = $stmt->fetchAll();

// คำนวณยอดรวมของทุก period เพื่อใช้เป็น total_due per member (สมมติทุกคนต้องจ่ายทุก period)
$stmt = $pdo->query("SELECT IFNULL(SUM(amount_due),0) AS total_due FROM payment_period");
$total_due_all_periods = (float)$stmt->fetchColumn();
?>

<link rel="stylesheet" href="assets/members_list.css">

<h2>Members List (สถานะการจ่ายรวม)</h2>

<p style="color: #666; margin-bottom: 20px;">
    ยอดรวมที่ต้องชำระในทุกงวด: <strong><?php echo number_format($total_due_all_periods, 2); ?> ฿</strong>
</p>

<table class="styled-table">
<thead>
    <tr>
        <th>Member (Role)</th>
        <th style="text-align:right;">Paid (Total)</th>
        <th style="text-align:right;">Pending (Total)</th>
        <th style="text-align:right;">Unpaid (Estimate)</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
<?php foreach($members as $m): 
    $member_id = $m['member_id'];
    
    // total paid by this member (status=Paid)
    $stmt = $pdo->prepare("SELECT IFNULL(SUM(amount_paid),0) FROM payment WHERE member_id=? AND status='Paid'");
    $stmt->execute([$member_id]);
    $paid = (float)$stmt->fetchColumn();

    // total pending by this member (status=Pending)
    $stmt = $pdo->prepare("SELECT IFNULL(SUM(amount_paid),0) FROM payment WHERE member_id=? AND status='Pending'");
    $stmt->execute([$member_id]);
    $pending = (float)$stmt->fetchColumn();

    // Unpaid is total due minus what has been paid or is currently pending review
    $unpaid = $total_due_all_periods - ($paid + $pending);
    if ($unpaid < 0) $unpaid = 0;
?>
    <tr>
        <td>
            <strong><?php echo htmlspecialchars($m['full_name']); ?></strong> 
            <span style="font-size:0.8em; color:#999;">(<?php echo htmlspecialchars(ucfirst($m['role'])); ?>)</span>
        </td>
        <td style="text-align:right;">
            <span class="status-badge status-paid"><?php echo number_format($paid,2); ?></span>
        </td>
        <td style="text-align:right;">
            <span class="status-badge status-pending"><?php echo number_format($pending,2); ?></span>
            <?php if ($pending > 0): ?>
                <a href='verify_payments.php' style="font-size:0.8em; margin-left:5px;">[Verify]</a>
            <?php endif; ?>
        </td>
        <td style="text-align:right;">
            <span class="status-badge status-unpaid"><?php echo number_format($unpaid,2); ?></span>
        </td>
        <td class="status-action">
            <a href='list_payments.php?member_id=<?php echo $member_id; ?>'>View details</a>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php include __DIR__ . '/footer.php'; ?>
