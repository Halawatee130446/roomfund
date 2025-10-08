<?php
// delete_period.php
require_once 'functions.php';
require_login();

if (!is_treasurer()) {
    flash('Access denied');
    header('Location: member_dashboard.php');
    exit;
}

require_once 'db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('Invalid period id');
    header('Location: list_periods.php');
    exit;
}

try {
    // เริ่ม transaction
    $pdo->beginTransaction();

    // (1) ถ้าต้องการ ลบไฟล์ receipt ที่เกี่ยวข้องก่อน
    // ดึงรายชื่อไฟล์ receipt ของ payment ที่เกี่ยวกับ period นี้
    $stmt = $pdo->prepare("SELECT receipt_filename FROM payment WHERE period_id = ?");
    $stmt->execute([$id]);
    $receipts = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // ลบไฟล์จาก disk (ถ้าเก็บในโฟลเดอร์ uploads/)
    foreach ($receipts as $rf) {
        if (empty($rf)) continue;
        // สร้าง path ที่คาดว่าเก็บไฟล์ (ปรับให้ตรงกับโปรเจคของคุณถ้าจำเป็น)
        $filePath = __DIR__ . '/' . ltrim($rf, '/\\');
        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    // (2) ลบ payments ที่เกี่ยวข้อง
    $stmt = $pdo->prepare("DELETE FROM payment WHERE period_id = ?");
    $stmt->execute([$id]);

    // (3) ลบ payment_period เอง
    $stmt = $pdo->prepare("DELETE FROM payment_period WHERE period_id = ?");
    $stmt->execute([$id]);

    // commit transaction
    $pdo->commit();

    flash('Deleted period and related payments.');
    header('Location: list_periods.php');
    exit;
} catch (PDOException $ex) {
    // rollback on error
    if ($pdo->inTransaction()) $pdo->rollBack();
    // บันทึก/แสดง error สำหรับดีบัก (ใน production ควร log แทนแสดง)
    flash('Error deleting period: ' . $ex->getMessage());
    header('Location: list_periods.php');
    exit;
}
