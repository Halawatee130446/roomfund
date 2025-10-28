<?php
// delete_period.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();

if (!is_treasurer()) {
    flash('Access denied');
    header('Location: ' . BASE_URL . ' /views/member_dashboard.php');
    exit;
}

 

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('Invalid period id');
    header('Location: ' . BASE_URL . ' /views/list_periods.php');
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT receipt_filename FROM payment WHERE period_id = ?");
    $stmt->execute([$id]);
    $receipts = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($receipts as $rf) {
        if (empty($rf)) continue;
        $filePath = __DIR__ . '/uploads/' . basename($rf);
        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM payment WHERE period_id = ?");
    $stmt->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM payment_period WHERE period_id = ?");
    $stmt->execute([$id]);
    $pdo->commit();

    flash('Deleted period and related payments.');
    header('Location: ' . BASE_URL . ' /views/list_periods.php');
    exit;
} catch (PDOException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('Error deleting period: ' . $ex->getMessage());
    header('Location: ' . BASE_URL . ' /views/list_periods.php');
    exit;
}
