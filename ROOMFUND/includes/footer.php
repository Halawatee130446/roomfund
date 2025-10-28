<?php
// footer.php
?>
</div>
<footer style="
    width: 100%;
    padding: 15px 0;
    margin-top: 20px;
    box-sizing: border-box; 
    border-top: 1px solid #ddd;
    background: #ffffff;
    color: #888;
    font-size: 0.85em;
    text-align: center;
">
    Project RoomFund
    <span style="margin: 0 10px; color: #ccc;">|</span>
    <a href="../notesFooter/grading_checklist.php" style="color: #888; text-decoration: none;">Grading Checklist</a>
    <span style="margin: 0 10px; color: #ccc;">|</span>
    <a href="../notesFooter/manual_checklist.php" style="color: #888; text-decoration: none;">Manual Test</a>
    <div style="font-size:0.75em; margin-top:5px; color:#aaa;">(Powered by PHP/MySQL)</div>
</footer>

<script>
(function () {
    var key = 'sidebarCollapsed';
    var body = document.body;
    function applyState(val) {
        if (val === '1') body.classList.add('sidebar-collapsed');
        else body.classList.remove('sidebar-collapsed');
    }
    function onReady(fn) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }
    onReady(function () {
        var saved = null;
        try { saved = localStorage.getItem(key); } catch (e) {}
        if (saved !== null) applyState(saved);
        else if (document.documentElement.classList.contains('sidebar-collapsed-early')) applyState('1');
        var btn = document.getElementById('sidebarToggle') || document.querySelector('[id="sidebarToggle"]');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var isCollapsed = body.classList.toggle('sidebar-collapsed');
            try { localStorage.setItem(key, isCollapsed ? '1' : '0'); } catch (err) {}
        });
    });
})();
</script>
</body>
</html>
