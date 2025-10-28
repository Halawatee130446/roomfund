<?php
// member_sidebar.php
require_once __DIR__ . '/functions.php';
start_secure_session();
if (!is_logged_in()) return;

$u = current_user();
$currentFile = basename($_SERVER['PHP_SELF']);

$links = [
    'Dashboard' => '../views/member_dashboard.php',
    'My Payments' => '../views/list_payments.php?member_id=' . intval($u['member_id']),
    'All Periods' => '../views/list_periods.php',
    'View Expense' => '../views/list_expenses.php',
    'Edit Profile' => '../forms/edit_profile.php',
    'Logout' => '../codebackend/logout.php',
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

<link rel="stylesheet" href="../assets/member_sidebar.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
  const body = document.body;
  const insideBtn = document.getElementById('toggleSidebarBtn');
  const handle = document.getElementById('memberSidebarHandle');
  const storageKey = 'memberSidebarCollapsed';

  function setCollapsed(val) {
    if (val) body.classList.add('sidebar-collapsed');
    else body.classList.remove('sidebar-collapsed');
    try { localStorage.setItem(storageKey, val ? '1' : '0'); } catch(e){}
  }

  try {
    if (localStorage.getItem(storageKey) === '1') setCollapsed(true);
  } catch(e){}

  if (insideBtn) {
    insideBtn.addEventListener('click', function(){
      const collapsed = body.classList.toggle('sidebar-collapsed');
      try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch(e){}
    });
  }

  if (handle) {
    handle.addEventListener('click', function(){
      setCollapsed(false);
    });
  }
});
</script>
