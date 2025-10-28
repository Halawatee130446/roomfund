<?php
// list_expenses.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
 

if (!is_treasurer()) {
    include ROOT_PATH . '/includes/member_sidebar.php';
}

include ROOT_PATH . '/includes/header.php';

$stmt = $pdo->query("SELECT e.*, m.full_name FROM expense e LEFT JOIN member m ON e.created_by=m.member_id ORDER BY e.expense_date DESC");
$rows = $stmt->fetchAll();

$total_expense = array_sum(array_column($rows, 'amount'));
?>

<link rel="stylesheet" href="../assets/list_expenses.css">
<h2>Expenses (รายการค่าใช้จ่าย)</h2>

<?php if (is_treasurer()): ?>
    <p><a href="../forms/add_expense.php" class="action-link">+ Add New Expense</a></p>
<?php endif; ?>

<table class="styled-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Description</th>
            <th>Category / Activity</th>
            <th style="text-align:right;">Amount (฿)</th>
            <th>Created by</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?php echo $r['expense_id'];?></td>
            <td><?php echo htmlspecialchars($r['expense_date']);?></td>
            <td><?php echo htmlspecialchars($r['description']);?></td>
            <td><?php echo htmlspecialchars($r['category']) . ' / ' . htmlspecialchars($r['activity']);?></td>
            <td style="text-align:right; font-weight:500; color: #cc0000;"><?php echo number_format($r['amount'], 2);?></td>
            <td><?php echo htmlspecialchars($r['full_name']);?></td>
        </tr>
        <?php endforeach; ?>
        
        <tr class="total-row">
            <td colspan="4" style="text-align:left;">Total Expenses:</td>
            <td style="text-align:right;"><?php echo number_format($total_expense, 2);?></td>
            <td></td>
        </tr>
    </tbody>
</table>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
