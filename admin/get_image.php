<?php
// إعداد الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin";

$conn = new mysqli($servername, $username, $password, $database);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// جلب صورة المدير من قاعدة البيانات
$id = 1; // نفترض أن الـ ID ثابت للمدير
$sql = "SELECT photo FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($photo);
$stmt->fetch();
$stmt->close();
$conn->close();

// إرسال الصورة إذا وُجدت
if ($photo) {
    header("Content-Type: image/jpeg"); // أو حسب نوع الصورة المخزنة
    echo $photo;
} else {
    readfile("default.png"); // صورة افتراضية في حال عدم العثور على صورة
}
?>
