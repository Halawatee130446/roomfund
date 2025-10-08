<?php
// member_sidebar.php (toggle + visible handle when collapsed)
require_once __DIR__ . '/functions.php';
start_secure_session();
if (!is_logged_in()) return;

$u = current_user();
$currentFile = basename($_SERVER['PHP_SELF']);

$links = [
    'Dashboard' => 'member_dashboard.php',
    'My Payments' => 'list_payments.php?member_id=' . intval($u['member_id']),
    'All Periods' => 'list_periods.php',
    // ลบ: 'Submit Payment' => 'payment_form.php',
    // เพิ่ม: 'View Expense' => 'list_expenses.php' (สมมติชื่อไฟล์)
    'View Expense' => 'list_expenses.php',
    'Edit Profile' => 'edit_profile.php',
    'Logout' => 'logout.php',
];

function link_is_active_member($href, $currentFile, $currentMemberId) {
    $hrefFile = preg_replace('/\\?.*$/','',$href);
    $hrefBase = basename($hrefFile);
    if ($hrefBase === $currentFile) return true;
    if ($currentFile === 'list_payments.php') {
        if (isset($_GET['member_id']) && (int)$_GET['member_id'] === (int)$currentMemberId) {
            if (strpos($href, 'list_payments.php') !== false) return true;
        }
    }
    return false;
}
?>

<div id="memberSidebar" class="sidebar">
  <div class="sidebar-header">
    <button id="toggleSidebarBtn" class="inside-toggle" title="Toggle menu">☰</button>
    <strong>Member Menu</strong>
  </div>

  <ul class="sidebar-list">
    <?php foreach($links as $title => $href):
        $active = link_is_active_member($href, $currentFile, $u['member_id']);
    ?>
      <li class="<?php echo $active ? 'active' : ''; ?>">
        <a href="<?php echo htmlspecialchars($href); ?>"><?php echo htmlspecialchars($title); ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<button id="memberSidebarHandle" class="sidebar-handle" aria-label="Open menu" title="Open menu">☰</button>

<link rel="stylesheet" href="assets/member_sidebar.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
  const body = document.body;
  const insideBtn = document.getElementById('toggleSidebarBtn');
  const handle = document.getElementById('memberSidebarHandle');
  const storageKey = 'memberSidebarCollapsed';

  // helper to set state and persist
  function setCollapsed(val) {
    if (val) body.classList.add('sidebar-collapsed');
    else body.classList.remove('sidebar-collapsed');
    try { localStorage.setItem(storageKey, val ? '1' : '0'); } catch(e){}
  }

  // init from storage
  try {
    if (localStorage.getItem(storageKey) === '1') setCollapsed(true);
  } catch(e){}

  // click inside toggle (when sidebar open)
  if (insideBtn) {
    insideBtn.addEventListener('click', function(e){
      const collapsed = body.classList.toggle('sidebar-collapsed');
      try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch(e){}
    });
  }

  // click handle (when sidebar collapsed) - opens sidebar
  if (handle) {
    handle.addEventListener('click', function(e){
      setCollapsed(false);
    });
  }
});
</script>