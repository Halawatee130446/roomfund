<?php
// footer.php (ปรับปรุงให้มี Footer กว้างเต็มจอ และ JS Toggle ที่แข็งแรง)
?>
</div> <footer style="
    /* Positioning & Layout */
    width: 100%;
    /* ใช้ padding-left 0 และ padding-right 0 เพื่อให้เนื้อหาชิดขอบ */
    padding: 15px 0;
    margin-top: 20px;
    box-sizing: border-box; 
    
    /* Aesthetics */
    border-top: 1px solid #ddd;
    background: #ffffff; /* ใช้สีขาวสะอาดตา */
    color: #888;
    font-size: 0.85em;
    text-align: center;
">
    Project RoomFund
    <span style="margin: 0 10px; color: #ccc;">|</span>
    <a href="grading_checklist.php" style="color: #888; text-decoration: none;">Grading Checklist</a>
    <span style="margin: 0 10px; color: #ccc;">|</span>
    <a href="manual_checklist.php" style="color: #888; text-decoration: none;">Manual Test</a>
    <div style="font-size:0.75em; margin-top:5px; color:#aaa;">(Powered by PHP/MySQL)</div>
</footer>

<script>
    (function () {
        var key = 'sidebarCollapsed';
        var body = document.body;
        
        function applyState(val) {
            if (val === '1') {
                body.classList.add('sidebar-collapsed');
            } else {
                body.classList.remove('sidebar-collapsed');
            }
        }

        function onReady(fn) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fn);
            } else {
                fn();
            }
        }

        onReady(function () {
            var saved = null;
            try { saved = localStorage.getItem(key); } catch (e) { console && console.warn && console.warn('localStorage read failed', e); }
            if (saved !== null) {
                applyState(saved);
            } else {
                if (document.documentElement.classList.contains('sidebar-collapsed-early')) {
                    applyState('1');
                }
            }

            var btn = document.getElementById('sidebarToggle');
            if (!btn) {
                btn = document.querySelector('[id="sidebarToggle"]') || null;
            }
            if (!btn) {
                return;
            }

            btn.addEventListener('click', function (e) {
                var isCollapsed = body.classList.toggle('sidebar-collapsed');
                try { localStorage.setItem(key, isCollapsed ? '1' : '0'); } catch (err) { console && console.warn && console.warn('localStorage set failed', err); }
            });
        });
    })();
</script>
</body>
</html>