<?php
// create_period.php
require_once 'functions.php';
require_login();
if (!is_treasurer()) { flash('Access denied'); header('Location: member_dashboard.php'); exit; }
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { 
        $error='Invalid CSRF'; 
    }
    
    $month = (int)($_POST['month'] ?? 0);
    $year = (int)($_POST['year'] ?? 0);
    $amount = (float)($_POST['amount_due'] ?? 0);
    $due = $_POST['due_date'] ?? null;
    
    if (!$error) {
        if ($month < 1 || $month > 12) {
            $error = "Month must be between 1 and 12.";
        } elseif ($year < 2000) {
            $error = "Year seems too low.";
        } elseif ($amount <= 0) {
            $error = "Amount Due must be greater than zero.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO payment_period (month, year, amount_due, due_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$month,$year,$amount,$due]);
                flash('Period created successfully.');
                header('Location: list_periods.php'); 
                exit;
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

include 'header.php';
?>
<link rel="stylesheet" href="assets/create_period.css">
<div class="form-card">
    <h2 style="text-align: center">Create New Payment Period</h2>

    <?php if (!empty($error)) echo "<div class='flash-error'>".htmlspecialchars($error)."</div>"; ?>

    <form method="post" action="create_period.php" class="styled-form">
        <input type="hidden" name="csrf" value="<?php echo csrf_token();?>">
        
        <label for="month">Month (1-12):</label>
        <input type="number" name="month" id="month" required min="1" max="12" value="<?php echo htmlspecialchars($_POST['month'] ?? date('n')); ?>">
        
        <label for="year">Year (e.g., 2025):</label>
        <input type="number" name="year" id="year" required min="2000" value="<?php echo htmlspecialchars($_POST['year'] ?? date('Y')); ?>">
        
        <label for="amount_due">Amount Due (฿):</label>
        <input type="number" name="amount_due" id="amount_due" step="0.01" required value="<?php echo htmlspecialchars($_POST['amount_due'] ?? '0.00'); ?>">
        
        <label for="due_date">Due date (YYYY-MM-DD) (Optional):</label>
        <input type="date" name="due_date" id="due_date" value="<?php echo htmlspecialchars($_POST['due_date'] ?? ''); ?>">
        
        <button type="submit">Create Period</button>
    </form>
</div>

<?php include 'footer.php'; ?>
