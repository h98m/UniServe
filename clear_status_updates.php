<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "leave_requests";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

$student_number = $_GET['student_number'] ?? null;
if (!$student_number) {
    echo json_encode(["error" => "معرف الطالب غير صالح"]);
    exit();
}

// تحديث status_updated إلى 0 عند قراءة الإشعارات
$query = "UPDATE leaves SET status_updated = 0 WHERE student_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_number);
$stmt->execute();

echo json_encode(["success" => true]);
?>
