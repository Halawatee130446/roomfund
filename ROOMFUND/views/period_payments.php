<?php
// period_payments.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
if (!is_treasurer()) { flash('Access denied'); header('Location: ' . BASE_URL . ' /views/member_dashboard.php'); exit; }
 
include ROOT_PATH . '/includes/header.php';

$period_id = isset($_GET['period_id']) && (int)$_GET['period_id'] ? (int)$_GET['period_id'] : null;

if (!$period_id) {
    $stmt = $pdo->query("SELECT period_id FROM payment_period ORDER BY year DESC, month DESC LIMIT 1");
    $row = $stmt->fetch();
    if ($row) {
        $period_id = (int)$row['period_id'];
    } else {
        echo "<p>No periods defined. <a href='../forms/create_period.php'>Create a period</a></p>";
        include ROOT_PATH . '/includes/footer.php';
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM payment_period WHERE period_id = ?");
$stmt->execute([$period_id]);
$period = $stmt->fetch();
if (!$period) {
    echo "<p>Period not found. <a href='../views/treasurer_dashboard.php'>Back</a></p>";
    include ROOT_PATH . '/includes/footer.php';
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) FROM member");
$total_members = (int)$stmt->fetchColumn();

$sql = "SELECT m.member_id, m.full_name, m.role, p.payment_id, p.amount_paid, p.status, p.receipt_filename
        FROM member m
        LEFT JOIN payment p ON p.member_id = m.member_id AND p.period_id = ?
        ORDER BY m.full_name";
$stmt = $pdo->prepare($sql);
$stmt->execute([$period_id]);
$rows = $stmt->fetchAll();

$periods = $pdo->query("SELECT period_id, month, year, amount_due FROM payment_period ORDER BY year DESC, month DESC")->fetchAll();
?>

<link rel="stylesheet" href="../assets/period_payments.css">

<h2>Payments for Period: Month <?php echo $period['month']; ?> / <?php echo $period['year']; ?></h2>

<form method="get" action="<?php echo BASE_URL; ?>/views/period_payments.php" class="period-selector-group">
    <label style="font-weight: bold; color: #555;">เลือกงวด: </label>
    <select name="period_id">
        <?php foreach($periods as $p): ?>
            <option value="<?php echo $p['period_id']; ?>" <?php echo ($p['period_id'] == $period_id) ? "selected" : ""; ?>>
                เดือน <?php echo htmlspecialchars($p['month']); ?> / <?php echo htmlspecialchars($p['year']); ?> (<?php echo number_format($p['amount_due'], 2); ?> ฿)
            </option>
        <?php endforeach; ?>
    </select> 
    <button type="submit">View Report</button>
</form>

<p style="margin-top: 15px; font-size: 1em; color: #666;">
    Total members: <strong><?php echo $total_members; ?></strong> (รวมแอดมิน) | Amount Due per Member: <strong><?php echo number_format($period['amount_due'], 2); ?> ฿</strong>
</p>

<table class="styled-table">
<thead>
    <tr>
        <th style="width: 5%;">#</th>
        <th style="width: 30%;">Member (Role)</th>
        <th style="width: 15%; text-align:center;">Status</th>
        <th style="width: 15%; text-align:right;">Amount Paid</th>
        <th style="width: 20%; text-align:center;">Receipt</th>
        <th style="width: 15%; text-align:center;">Action</th>
    </tr>
</thead>
<tbody>
<?php 
$i = 0;
foreach ($rows as $r):
    $i++;
    $memberName = htmlspecialchars($r['full_name']);
    $role = htmlspecialchars($r['role']);
    $status = $r['payment_id'] ? $r['status'] : 'Unpaid';
    $amount = $r['payment_id'] ? number_format((float)$r['amount_paid'],2) : '-';
    $receipt = $r['receipt_filename'] ?? '';

    $status_class = 'status-unpaid';
    $status_label = 'Unpaid';
    $action_html = '-';
    
    if ($status === 'Paid') {
        $status_class = 'status-paid';
        $status_label = 'Paid';
        $action_html = 'Completed';
    } elseif ($status === 'Pending') {
        $status_class = 'status-pending';
        $status_label = 'Pending';
        $action_html = "<a href='verify_payments.php' class='action-link'>Verify Now</a>";
    } elseif ($status === 'Waived') {
        $status_class = 'status-waived';
        $status_label = 'Waived';
        $action_html = 'Exempted';
    } else {
        $status_class = 'status-unpaid';
        $status_label = 'Unpaid';
    }

    if (!empty($receipt)) {
        if (strpos($receipt, '/') === false) {
            $receipt_url = '../uploads/' . $receipt;
        } else {
            $receipt_url = $receipt;
        }
    } else {
        $receipt_url = '';
    }
?>
    <tr>
        <td style="text-align:center;"><?php echo $i; ?></td>
        <td>
            <strong><?php echo $memberName; ?></strong>
            <div style="font-size:0.8em; color:#999;"><?php echo ucfirst($role); ?></div>
        </td>
        <td style="text-align:center;">
            <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($status_label); ?></span>
        </td>
        <td style="text-align:right; font-weight: 500;"><?php echo $amount; ?></td>
        <td style="text-align:center;">
            <?php if (!empty($receipt_url)): ?>
                <a href="<?php echo htmlspecialchars($receipt_url); ?>" target="_blank" class="action-link" style="color:#007bff;">View</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
        <td style="text-align:center;">
            <?php echo $action_html; ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<p style='margin-top:20px;'>
    <a href='treasurer_dashboard.php' class="action-link" style="padding: 8px 15px; border-radius: 8px; border: 1px solid #ddd;">← Back to Dashboard</a>
</p>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
