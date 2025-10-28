<?php
// edit_payment_channels.php
 require_once __DIR__ . '/../includes/init.php'; 
require_login();
if (!is_treasurer()) { flash('Access denied'); header('Location: ' . BASE_URL . ' /views/member_dashboard.php'); exit; }
 

$channels_file = ROOT_PATH .'/codebackend/payment_channels.php';
$channels = [];
if (file_exists($channels_file)) {
    $channels = include $channels_file;
    if (!is_array($channels)) $channels = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { flash('Invalid CSRF'); header('Location: ' . BASE_URL . ' /forms/edit_payment_channels.php'); exit; }
    $banks = $_POST['bank'] ?? [];
    $nos = $_POST['account_no'] ?? [];
    $names = $_POST['account_name'] ?? [];
    $notes = $_POST['note'] ?? [];

    $new = [];
    for ($i=0; $i < count($banks); $i++) {
        $b = trim($banks[$i] ?? '');
        $no = trim($nos[$i] ?? '');
        $an = trim($names[$i] ?? '');
        $nt = trim($notes[$i] ?? '');
        if ($b === '' && $no === '' && $an === '') continue;
        $b = strip_tags($b);
        $no = strip_tags($no);
        $an = strip_tags($an);
        $nt = strip_tags($nt);
        $new[] = ['bank'=>$b, 'account_no'=>$no, 'account_name'=>$an, 'note'=>$nt];
    }

    if (write_php_array_file($channels_file, $new)) {
        flash('Payment channels updated');
    } else {
        flash('Unable to write channels file. Check file permissions.');
    }
    header('Location: ' . BASE_URL . ' /forms/edit_payment_channels.php'); exit;
}

include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="../assets/edit_payment_channels.css">
<div class="form-card">
    <h2>Edit Payment Channels</h2>
    <p>เพิ่ม/แก้ไข ช่องทางการชำระ</p>

    <form method="post" action="<?php echo BASE_URL; ?>/forms/edit_payment_channels.php">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        
        <table class="styled-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Bank</th>
                    <th style="width: 25%;">Account No.</th>
                    <th style="width: 25%;">Account Name</th>
                    <th style="width: 20%;">Note</th>
                    <th style="width: 10%; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody id="channels-tbody">
                <?php
                $count = max(1, count($channels));
                for ($i=0; $i < $count; $i++):
                    $ch = $channels[$i] ?? ['bank'=>'','account_no'=>'','account_name'=>'','note'=>''];
                ?>
                <tr>
                    <td><input name="bank[]" value="<?php echo htmlspecialchars($ch['bank']); ?>"></td>
                    <td><input name="account_no[]" value="<?php echo htmlspecialchars($ch['account_no']); ?>"></td>
                    <td><input name="account_name[]" value="<?php echo htmlspecialchars($ch['account_name']); ?>"></td>
                    <td><input name="note[]" value="<?php echo htmlspecialchars($ch['note']); ?>"></td>
                    <td style="text-align: center;"><button type="button" onclick="this.closest('tr').remove();" class="styled-button-remove">Remove</button></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        
        <p style="text-align: right; margin-top: 15px;">
            <button type="button" onclick="addRow()" class="styled-button" style="width: auto;">+ Add Channel</button>
        </p>

        <div class="save-button-container">
            <button type="submit" class="styled-button">Save Channels</button>
        </div>
    </form>

<script>
function addRow(){
    const tableBody = document.getElementById('channels-tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input name="bank[]"></td>
        <td><input name="account_no[]"></td>
        <td><input name="account_name[]"></td>
        <td><input name="note[]"></td>
        <td style="text-align: center;"><button type="button" onclick="this.closest('tr').remove();" class="styled-button-remove">Remove</button></td>
    `;
    tableBody.appendChild(newRow);
}
</script>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
