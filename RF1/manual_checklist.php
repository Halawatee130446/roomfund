<?php
// manual_checklist.php
include 'header.php';
echo "<h2>Manual Test Checklist</h2>";
echo "<ol>
<li>Run setup.php once -> DB and sample accounts created.</li>
<li>Login as member/member and treasurer/treasurer (credentials shown in setup.php).</li>
<li>Member: View periods, submit payment with receipt (jpg/pdf).</li>
<li>Treasurer: View pending payments, approve/reject/waive.</li>
<li>Create/Edit/Delete payment periods.</li>
<li>Add expense and check expense list.</li>
<li>Export CSV for payments and expenses.</li>
<li>Verify prepared statements & input validation by trying malicious input (basic).</li>
</ol>";
include 'footer.php';
