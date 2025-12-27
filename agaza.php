<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// استقبال البيانات من النموذج
$studentName = $_POST['studentName'];
$studentNumber = $_POST['studentNumber'];
$leaveType = $_POST['leaveType'];
$hospital = isset($_POST['hospital']) ? $_POST['hospital'] : null;
$relation = isset($_POST['relation']) ? $_POST['relation'] : null;
$requestType = "اجازة"; // تعيين القيمة الثابتة

// تحويل الصورة إلى بيانات ثنائية (blob)
$medicalFileData = null;
$deathProofData = null;

if ($leaveType === "مرضية" && isset($_FILES['medicalFile']['tmp_name'])) {
    $medicalFileData = file_get_contents($_FILES['medicalFile']['tmp_name']);
}

if ($leaveType === "وفاة" && isset($_FILES['deathProof']['tmp_name'])) {
    $deathProofData = file_get_contents($_FILES['deathProof']['tmp_name']);
}

// إدخال البيانات في قاعدة البيانات
$sql = "INSERT INTO leaves (student_name, student_number, leave_type, hospital, relation, medical_file, death_proof, request_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $studentName, $studentNumber, $leaveType, $hospital, $relation, $medicalFileData, $deathProofData, $requestType);

if ($stmt->execute()) {
    echo "تم تقديم الطلب بنجاح!";
} else {
    echo "خطأ في تقديم الطلب: " . $stmt->error;
}

// إغلاق الاتصال
$stmt->close();
$conn->close();

// إعادة توجيه المستخدم بعد الإرسال
header("Location: TLPAT.php");
exit();
?>
