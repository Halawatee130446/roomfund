<?php
// admin_sidebar.php
// Sidebar สำหรับผู้ดูแล (treasurer) — แสดง Admin Menu + Member Menu พร้อมกัน
require_once __DIR__ . '/functions.php';
start_secure_session();
if (!is_treasurer()) {
    return;
}

$u = current_user();
$currentFile = basename($_SERVER['PHP_SELF']);

// admin links (title => href)
$admin_links = [
    'Dashboard' => 'treasurer_dashboard.php',
    'Members' => 'members_list.php',
    'Periods' => 'list_periods.php',
    'All Payments' => 'period_payments.php',
    'Verify (Pending)' => 'verify_payments.php',
    'Expenses' => 'list_expenses.php',
    'Create Period' => 'create_period.php',
    'Edit Channels' => 'edit_payment_channels.php',
];

// member links (use current admin member_id)
$member_links = [
    'My Dashboard' => 'member_dashboard.php',
    'My Payments' => 'list_payments.php?member_id=' . intval($u['member_id']),
    'edit Profile' => 'edit_profile.php',
    'Logout' => 'logout.php',
];

// helper: returns true if a link href should be considered "active"
function link_is_active($href, $currentFile, $currentMemberId) {
    // strip query string
    $hrefFile = preg_replace('/\\?.*$/','',$href);
    $hrefBase = basename($hrefFile);

    if ($hrefBase === $currentFile) {
        // if href contains member_id param, ensure it matches currentMemberId
        if (strpos($href, 'member_id=') !== false) {
            // parse query
            $q = parse_url($href, PHP_URL_QUERY);
            parse_str($q ?: '', $qs);
            if (!empty($qs['member_id'])) {
                return (int)$qs['member_id'] === (int)$currentMemberId;
            }
        }
        return true;
    }

    // special case: when viewing member payments page without member_id param but on member_dashboard.php
    if ($currentFile === 'member_dashboard.php' && in_array($hrefBase, ['list_payments.php','member_dashboard.php'])) {
        // highlight My Dashboard / My Payments when on member dashboard
        return true;
    }

    // also consider list_payments.php?member_id=currentMemberId active when currentFile is list_payments.php and REQUEST has that member_id
    if ($currentFile === 'list_payments.php') {
        if (isset($_GET['member_id']) && (int)$_GET['member_id'] === (int)$currentMemberId) {
            // if href points to the same member_id, mark active
            if (strpos($href, 'member_id=') !== false) {
                $q = parse_url($href, PHP_URL_QUERY);
                parse_str($q ?: '', $qs);
                if (!empty($qs['member_id']) && (int)$qs['member_id'] === (int)$currentMemberId) return true;
            }
        }
    }

    return false;
}
?>
<style>
  a {
    color: inherit;
    transition: color 0.2s ease;
    font-size: 0.8em;
}
</style>
<nav id="adminSidebar" aria-label="Admin sidebar">
  <div class="sidebar-header"><strong>Admin Menu</strong></div>
  <ul class="sidebar-list">
    <?php foreach($admin_links as $title => $href):
        $active = link_is_active($href, $currentFile, $u['member_id']);
    ?>
      <li class="<?php echo $active ? 'active' : ''; ?>">
        <a href="<?php echo htmlspecialchars($href); ?>"><?php echo htmlspecialchars($title); ?></a>
      </li>
    <?php endforeach; ?>
  </ul>

  <hr/>

  <div class="sidebar-header"><strong>Member Menu</strong></div>
  <ul class="sidebar-list">
    <?php foreach($member_links as $title => $href):
        $active = link_is_active($href, $currentFile, $u['member_id']);
    ?>
      <li class="<?php echo $active ? 'active' : ''; ?>">
        <a href="<?php echo htmlspecialchars($href); ?>"><?php echo htmlspecialchars($title); ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
