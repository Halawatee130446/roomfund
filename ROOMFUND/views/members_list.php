<?php
// members_list.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
if (!is_treasurer()) { flash('Access denied'); header('Location: ' . BASE_URL . ' /views/member_dashboard.php'); exit; }
 
include ROOT_PATH . '/includes/header.php';

$stmt = $pdo->query("SELECT member_id, full_name, role FROM member ORDER BY full_name"); 
$members = $stmt->fetchAll();

$stmt = $pdo->query("SELECT IFNULL(SUM(amount_due),0) AS total_due FROM payment_period");
$total_due_all_periods = (float)$stmt->fetchColumn();
?>

<link rel="stylesheet" href="../assets/members_list.css">

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
    </tr>
</thead>
<tbody>
<?php foreach($members as $m): 
    $member_id = $m['member_id'];
    
    $stmt = $pdo->prepare("SELECT IFNULL(SUM(amount_paid),0) FROM payment WHERE member_id=? AND status='Paid'");
    $stmt->execute([$member_id]);
    $paid = (float)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT IFNULL(SUM(amount_paid),0) FROM payment WHERE member_id=? AND status='Pending'");
    $stmt->execute([$member_id]);
    $pending = (float)$stmt->fetchColumn();

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
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
