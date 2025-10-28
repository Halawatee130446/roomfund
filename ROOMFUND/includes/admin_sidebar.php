<?php
// admin_sidebar.php
require_once __DIR__ . '/functions.php';
start_secure_session();
if (!is_treasurer()) {
    return;
}

$u = current_user();
$currentFile = basename($_SERVER['PHP_SELF']);

$admin_links = [
    'Dashboard' => '../views/treasurer_dashboard.php',
    'Members' => '../views/members_list.php',
    'Periods' => '../views/list_periods.php',
    'All Payments' => '../views/period_payments.php',
    'Verify (Pending)' => '../views/verify_payments.php',
    'Expenses' => '../views/list_expenses.php',
    'Create Period' => '../forms/create_period.php',
    'Edit Channels' => '../forms/edit_payment_channels.php',
];

$member_links = [
    'My Dashboard' => '../views/member_dashboard.php',
    'My Payments' => '../views/list_payments.php?member_id=' . intval($u['member_id']),
    'edit Profile' => '../forms/edit_profile.php',
    'Logout' => '../codebackend/logout.php',
];

function link_is_active($href, $currentFile, $currentMemberId) {
    $hrefFile = preg_replace('/\\?.*$/','',$href);
    $hrefBase = basename($hrefFile);

    if ($hrefBase === $currentFile) {
        if (strpos($href, 'member_id=') !== false) {
            $q = parse_url($href, PHP_URL_QUERY);
            parse_str($q ?: '', $qs);
            if (!empty($qs['member_id'])) {
                return (int)$qs['member_id'] === (int)$currentMemberId;
            }
        }
        return true;
    }

    if ($currentFile === 'member_dashboard.php' && in_array($hrefBase, ['list_payments.php','member_dashboard.php'])) {
        return true;
    }

    if ($currentFile === 'list_payments.php') {
        if (isset($_GET['member_id']) && (int)$_GET['member_id'] === (int)$currentMemberId) {
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
