<?php
// إعدادات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

// إنشاء الاتصال بقاعدة البيانات
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage();
    exit();
}

// التحقق من أن البيانات تم إرسالها
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // الحصول على القيم من النموذج
    $name = $_POST['studentName'];
    $studentId = $_POST['studentNumber'];
    $reason = $_POST['reason'];
    $requestType = 'طلب تخفيض القسط'; // إضافة نوع الطلب

    // تحضير الاستعلام لإدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO tchfed (student_name, student_number, reason, request_type) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // ربط القيم بالاستعلام وتنفيذ الاستعلام
    $stmt->execute([$name, $studentId, $reason, $requestType]);

    // التحقق من نجاح العملية
    if ($stmt->rowCount()) {
        echo "تم إرسال الطلب بنجاح!";
    } else {
        echo "فشل في إرسال الطلب.";
    }
    
    // إعادة التوجيه بعد تنفيذ العملية
    header("Location: TLPAT.php");
    exit();  // تأكد من إنهاء التنفيذ هنا
}

// إغلاق الاتصال
$pdo = null;
?>
