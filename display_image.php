<?php
session_start();

// بيانات الاتصال بقاعدة البيانات
$host = "localhost";
$dbname = "my_database";
$user = "root";
$password = "";

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($host, $user, $password, $dbname);

// التحقق من نجاح الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// تعيين الترميز لضمان دعم العربية
$conn->set_charset("utf8");

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // استعلام جلب الصورة من قاعدة البيانات
    $stmt = $conn->prepare("SELECT profile_pic FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();

    // التأكد من وجود بيانات
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($imageData);
        $stmt->fetch();

        // إرسال الصورة للمتصفح
        header("Content-Type: image/jpeg"); // غيّر إلى "image/png" إذا كانت الصورة PNG
        echo $imageData;
    } else {
        // إرسال صورة افتراضية إذا لم تكن هناك صورة مخزنة
        header("Content-Type: image/jpeg");
        readfile("default_profile.jpg");
    }

    // إغلاق الاستعلام والاتصال
    $stmt->close();
}

$conn->close();
?>
