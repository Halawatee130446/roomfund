<?php
// manual_checklist.php
require_once __DIR__ . '/../includes/init.php';
include ROOT_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="../assets/member_dashboard.css">

<div class="container" style="max-width:900px;margin:auto;padding:2rem;line-height:1.8">
  <h1>📘 RoomFund Manual & Run Guide</h1>
  <hr>

  <h2>1. วัตถุประสงค์ของระบบ</h2>
  <p>
    ระบบ <strong>RoomFund</strong> ถูกออกแบบเพื่อบริหารจัดการกองทุนค่าห้อง/ค่ากลาง
    โดยมีผู้ใช้งานหลัก 2 กลุ่ม:
  </p>
  <ul>
    <li><b>Member</b> – สมาชิกที่ชำระเงินค่าห้อง</li>
    <li><b>Treasurer</b> – เหรัญญิกผู้ตรวจสอบและอนุมัติการชำระ</li>
  </ul>

  <h2>2. ขั้นตอนการติดตั้งและเริ่มต้นใช้งาน</h2>
  <ol>
    <li>เปิดโปรแกรมจำลองเซิร์ฟเวอร์ เช่น XAMPP / WAMP / Laragon</li>
    <li>แตกไฟล์.zip</li>
    <li>คัดลอกโฟลเดอร์โปรเจกต์ <code>ROOMFUND</code> ไปไว้ในโฟลเดอร์ <code>htdocs</code></li>
    <li>เปิดเบราว์เซอร์และเข้า <code>http://localhost/ROOMFUND/setup.php</code> เพื่อสร้างฐานข้อมูล</li>
    <li>จากนั้นเข้า <code>http://localhost/ROOMFUND/start/login.php</code> เพื่อเข้าสู่ระบบ</li>
  </ol>

  <h2>3. บัญชีทดสอบระบบ</h2>
  <ul>
    <li>Member → username: <b>member</b> / password: <b>member123</b></li>
    <li>Treasurer → username: <b>treasurer</b> / password: <b>treasurer123</b></li>
  </ul>

  <h2>4. ฟังก์ชันหลักของระบบ</h2>
  <ul>
    <li>สมาชิก: เข้าระบบ / อัปโหลดสลิป / ดูประวัติการชำระ</li>
    <li>เหรัญญิก: สร้างงวด / ตรวจสอบสถานะ / บันทึกรายจ่าย</li>
    <li>ระบบ Dashboard แสดงยอดรวม / สถานะการชำระ</li>
  </ul>

  <h2>5. เครดิตผู้พัฒนา</h2>
  <p>
    ผู้พัฒนา: <b>ฮาลาวาตี อิศลามียกุล</b><br>
    รหัสนักศึกษา: 6620610118<br>
    คณะ: วิทยาการสื่อสาร <br>
    สาขา: คอมพิวเตอร์และวิทยาการสารสนเทศเพื่อการจัดการ<br>
    มหาวิทยาลัย: สงขลานครินทร์ วิทยาเขตปัตตานี<br>
    โปรเจกต์: RoomFund — ระบบเก็บเงินค่าห้อง<br>
    ภาคการศึกษา: 1/2568<br>

  </p>
</div>

<?php
include ROOT_PATH . '/includes/footer.php';
?>
