<?php
// header.php
require_once __DIR__ . '/functions.php';
start_secure_session();
$u = current_user();
$isAdmin = is_treasurer();
$bodyClass = $isAdmin ? 'has-sidebar' : '';

$currentPage = basename($_SERVER['PHP_SELF']);
$isPublicPage = in_array($currentPage, ['login.php', 'register.php']);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>Project RoomFund</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
  <script>
    (function () {
      try {
        var key = 'sidebarCollapsed';
        if (localStorage.getItem(key) === '1') {
          document.documentElement.classList.add('sidebar-collapsed-early');
        }
      } catch (e) {}
    })();
  </script>
  <style>
    body {
      font-family: 'Prompt', 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background: #f0f4f8;
      color: #333;
    }
    :root {
      --sidebar-w: 220px;
      --primary-pastel: #b4e1ff;
      --secondary-pastel: #ffd6e0;
      --accent-pastel: #a1e3d8;
      --text-color: #4c4c4c;
      --topbar-h: 55px;
    }
    #topbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: var(--topbar-h);
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(221, 221, 221, 0.5);
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 0 20px;
      box-sizing: border-box;
      z-index: 998;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    #topbar a {
      color: var(--text-color);
      text-decoration: none;
      padding: 5px 8px;
      border-radius: 8px;
      transition: background-color 0.2s ease, color 0.2s ease;
      font-weight: 500;
    }
    #topbar a:hover {
      background-color: var(--secondary-pastel);
      color: #333;
    }
    #sidebarToggle {
      background: none;
      border: none;
      font-size: 1.5em;
      cursor: pointer;
      color: var(--text-color);
      padding: 5px;
      border-radius: 6px;
      transition: background-color 0.2s;
    }
    #sidebarToggle:hover { background-color: #eee; }
    #adminSidebar {
      position: fixed;
      left: 0;
      top: var(--topbar-h);
      bottom: 0;
      width: var(--sidebar-w);
      background: #ffffff;
      border-right: 1px solid #eee;
      padding: 15px 10px;
      box-sizing: border-box;
      overflow-y: auto;
      transition: transform 0.22s ease;
      z-index: 999;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.02);
    }
    #adminSidebar .sidebar-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    #adminSidebar .sidebar-list li a {
      display: block;
      padding: 8px 10px;
      border-radius: 10px;
      text-decoration: none;
      color: var(--text-color);
      font-weight: 400;
    }
    #adminSidebar .sidebar-list li.active a,
    #adminSidebar .sidebar-list li a:hover {
      background: var(--accent-pastel);
      color: #333;
      font-weight: 600;
    }
    #content-wrapper {
      margin-top: calc(var(--topbar-h) + 15px);
      padding: 20px;
      transition: margin-left .22s ease;
      min-height: calc(100vh - var(--topbar-h) - 15px);
      box-sizing: border-box;
    }
    .has-sidebar #content-wrapper {
      margin-left: calc(var(--sidebar-w) + 30px);
    }
    .sidebar-collapsed #adminSidebar,
    .sidebar-collapsed-early #adminSidebar {
      transform: translateX(-100%);
    }
    .sidebar-collapsed .has-sidebar #content-wrapper,
    .sidebar-collapsed-early .has-sidebar #content-wrapper {
      margin-left: 30px;
    }
    @media(max-width:800px) {
      :root { --sidebar-w: 180px; }
      .has-sidebar #content-wrapper { margin-left: calc(var(--sidebar-w) + 20px); }
    }
    <?php if ($isPublicPage): ?>
      body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }
      #content-wrapper {
        margin-top: var(--topbar-h) !important;
        padding: 0 !important;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - var(--topbar-h)) !important;
      }
    <?php endif; ?>
    #flash-area span {
      background: var(--accent-pastel);
      color: #38761d;
      padding: 5px 10px;
      border-radius: 8px;
      font-size: 0.9em;
      font-weight: 500;
      animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
      from { transform: translateY(-10px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body class="<?php echo $bodyClass; ?>">
  <?php if ($isAdmin): include __DIR__ . '/admin_sidebar.php'; endif; ?>
  <div id="topbar">
    <?php if ($isAdmin): ?>
      <button id="sidebarToggle" aria-label="Toggle admin menu">☰</button>
    <?php endif; ?>
    <div style="font-weight: 600; font-size: 1.2em; color: #555;">
      <span style="color: #ff91a4;">Project</span> RoomFund
    </div>
    <?php if (is_logged_in()):
      $u = current_user(); ?>
      <div style="flex:1; display:flex; gap:15px; align-items:center;">
        <span style="font-size: 0.9em; color: #777;">
          เข้าสู่ระบบในชื่อ: <strong><?php echo htmlspecialchars($u['username']); ?></strong>
          (<?php echo htmlspecialchars($u['role']); ?>)
        </span>
        <nav style="margin-left: auto; display:flex; gap:10px;">
          <?php if ($u['role'] === 'treasurer'): ?>
            <a href="../views/treasurer_dashboard.php">Treasurer Dashboard</a>
          <?php endif; ?>
          <a href="../views/member_dashboard.php">Member Dashboard</a>
        </nav>
        <a href="../codebackend/logout.php" style="background-color: var(--secondary-pastel); font-weight: 600;">
          Logout</a>
      </div>
    <?php else: ?>
      <div style="flex:1; display:flex; justify-content: flex-end; gap: 10px;">
        <a href="../start/login.php">Login</a>
        <a href="../start/register.php" style="background-color: var(--primary-pastel); font-weight: 600;">ลงทะเบียน</a>
      </div>
    <?php endif; ?>
    <div id="flash-area">
      <?php if ($msg = flash()) echo "<span>" . htmlspecialchars($msg) . "</span>"; ?>
    </div>
  </div>
  <div id="content-wrapper">
