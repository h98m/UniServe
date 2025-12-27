<?php
$servername = "localhost";
$username = "root"; // تأكد من أنه مطابق لبياناتك
$password = ""; // إذا كنت تستخدم XAMPP أو MAMP، فغالبًا لا يوجد كلمة مرور
$database = "leave_requests"; // استبدلها باسم قاعدة بياناتك الفعلي

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $database);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>
