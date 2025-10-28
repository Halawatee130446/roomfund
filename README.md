# roomfund
โปรเจควิชา web application
# RoomFund — ระบบเก็บเงินค่าห้อง

## 📌 คำอธิบายระบบ
RoomFund คือระบบจัดการกองทุนค่าห้อง/กองกลางที่ออกแบบให้สมาชิกและเหรัญญิกสามารถบันทึกและตรวจสอบการชำระเงินได้อย่างโปร่งใส

- พัฒนาโดยใช้ **PHP + MySQL + HTML + CSS**
- แยกส่วนการทำงานด้วยโฟลเดอร์ชัดเจน 
- มีระบบจัดการสิทธิ์ผู้ใช้ 2 ระดับ: Member / Treasurer

---

## 🚀 ขั้นตอนการติดตั้งและรันระบบ

1. เปิดโปรแกรมจำลองเซิร์ฟเวอร์ (XAMPP, Laragon, WAMP)
2. รันไฟล์ Setup สร้างฐานข้อมูล
3. รันไฟล์ start/login.php
4. ใช้บัญชีสำหรับทดสอบ:
    Member accounts: 
        username=member / password=member123

    Treasurer accounts:
        username=treasurer / password=treasurer123

---

## 🧑‍💻 การพัฒนาและโค้ด
- เขียนด้วย PHP (โหมด procedural + include)
- เชื่อมต่อฐานข้อมูลด้วย PDO
- ทุกหน้าเรียก `includes/init.php` เพื่อเริ่ม session, เชื่อม DB, และใช้ฟังก์ชันรวม
- ใช้ CSS เฉพาะหน้าผ่าน `/assets/*.css` เพื่อปรับโทน UI แยกกันชัดเจน

---


