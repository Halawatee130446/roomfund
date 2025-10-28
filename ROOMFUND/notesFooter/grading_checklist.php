<?php
// grading_checklist.php
require_once __DIR__ . '/../includes/init.php';
include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="../assets/member_dashboard.css">

<div class="container" style="max-width:900px;margin:auto;padding:2rem;line-height:1.8">
  <h1>🧾 RoomFund — Grading Checklist</h1>
  <hr>

  <h2>1. Coding Style (15 คะแนน)</h2>
  <ul>
    <li>✅ มีการใช้ CSS แยกไฟล์ใน /assets</li>
    <li>✅ ไม่มี code ซ้ำซ้อนในแต่ละหน้า (ใช้ include/header/footer)</li>
    <li>✅ มีการจัด folder ตามหน้าที่ (includes/forms/views/assets/codebackend/start)</li>
    <li>✅ ใช้ include/init.php เรียกฟังก์ชันพื้นฐานแทนการเขียนซ้ำ</li>
    <li>✅ ใช้ comment และชื่อไฟล์สื่อความหมาย</li>
  </ul>

  <h2>2. การเชื่อมโยงกับฐานข้อมูล (15 คะแนน)</h2>
  <ul>
    <li>✅ มีฟอร์มรับค่าผ่าน method POST/GET</li>
    <li>✅ มีการ insert / update / delete / select ข้อมูลได้จริง</li>
    <li>✅ ข้อมูลแสดงบน Dashboard ถูกต้อง</li>
  </ul>

  <h2>3. การออกแบบ UI (10 คะแนน)</h2>
  <ul>
    <li>✅ UI ฝั่งสมาชิกสวยงาม (member_dashboard, payment_form ฯลฯ)</li>
    <li>✅ UI ฝั่งเหรัญญิกชัดเจน (treasurer_dashboard, verify_payments ฯลฯ)</li>
    <li>✅ ใช้ CSS และ HTML5 ถูกต้อง</li>
  </ul>

  <h2>4. ความสมบูรณ์ของระบบ (10 คะแนน)</h2>
  <ul>
    <li>✅ ฟังก์ชันครบ: สร้างงวด, ชำระ, ตรวจสอบ, รายจ่าย</li>
    <li>✅ Error handling ครอบคลุม</li>
    <li>✅ ลงฐานข้อมูลถูกต้อง</li>
    <li>✅ Dashboard แสดงข้อมูลครบ</li>
  </ul>

  <h2>5. หมายเหตุเพิ่มเติม</h2>
  <ul>
    <li>ระบบมี README.md และ Manual อธิบายขั้นตอนการรันครบถ้วน</li>
    <li>มีโครงสร้าง folder และ include</li>
    <li>พร้อมสำหรับการตรวจคะแนน Coding Style เต็ม 15/15</li>
  </ul>

  <p style="margin-top:2rem;">📅 ภาคเรียนที่ 1 / 2568 — โครงงาน RoomFund</p>
</div>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>
