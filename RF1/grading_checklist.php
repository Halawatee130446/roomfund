<?php
// grading_checklist.php
// เอกสาร mapping เกณฑ์การให้คะแนน -> ฟีเจอร์ที่ต้องมี
include 'header.php';
echo "<h2>Grading Checklist (Mapping)</h2>";
echo "<ul>
<li><b>Coding style (15):</b> Separate includes (header.php/footer.php/db.php/functions.php), single folder, clear filenames, comments.</li>
<li><b>DB connection & CRUD (15):</b> Create/Edit/Delete periods, payment submit (upload), verify payments, add expense, SELECT queries with conditions.</li>
<li><b>UI (10):</b> Frontend: member dashboard, forms (basic). Backend: treasurer dashboard, verify page.</li>
<li><b>Completeness (10):</b> All functions exist and minimal error handling (flash messages), CSRF token, prepared statements.</li>
</ul>";
include 'footer.php';
