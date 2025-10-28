<?php
// add_expense.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
if (!is_treasurer()) {
    flash('Access denied');
    header('Location: ' . BASE_URL . ' /views/member_dashboard.php');
    exit;
}
 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid CSRF';
    } else {
        $date = $_POST['expense_date'] ?? date('Y-m-d');
        $desc = $_POST['description'] ?? '';
        $cat = $_POST['category'] ?? '';
        $activity = $_POST['activity'] ?? '';
        $amount = (float) ($_POST['amount'] ?? 0);
        $created_by = current_user()['member_id'];
        if ($amount <= 0 || empty($desc)) {
            $error = "Amount must be greater than zero and Description cannot be empty.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO expense (expense_date, description, category, activity, amount, created_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$date, $desc, $cat, $activity, $amount, $created_by]);
                flash('Expense added');
                header('Location: ' . BASE_URL . ' /views/list_expenses.php');
                exit;
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>../assets/add_expense.css">
<div class="form-card">
    <h2>➕ Add New Expense</h2>

    <?php if (!empty($error))
        echo "<div class='flash-error'>" . htmlspecialchars($error) . "</div>"; ?>

    <form method="post" action="<?php echo BASE_URL; ?>/forms/add_expense.php" class="styled-form">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">

        <label for="expense_date">Date:</label>
        <input type="date" name="expense_date" id="expense_date" required
            value="<?php echo htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')); ?>">

        <label for="description">Description:</label>
        <input name="description" id="description" required
            value="<?php echo htmlspecialchars($_POST['description'] ?? ''); ?>">

        <label for="category">Category (e.g., Supplies, Repair):</label>
        <input name="category" id="category" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">

        <label for="activity">Activity (e.g., Project A, Maintenance):</label>
        <input name="activity" id="activity" value="<?php echo htmlspecialchars($_POST['activity'] ?? ''); ?>">

        <label for="amount">Amount (฿):</label>
        <input type="number" name="amount" id="amount" step="0.01" required
            value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">

        <button type="submit">Record Expense</button>
    </form>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>