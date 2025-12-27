<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "leave_requests"; // استبدلها باسم قاعدة بياناتك

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

$student_number = $_GET['student_number'] ?? null;
if (!$student_number) {
    echo json_encode(["error" => "معرف الطالب غير صالح"]);
    exit();
}

// الجداول التي تحتوي على طلبات الطلاب
$tables = ["leaves", "student_requests", "student_transfers", "tchfed"];
$total_notifications = 0;

foreach ($tables as $table) {
    $query = "SELECT COUNT(*) FROM $table WHERE student_number = ? AND status_updated = 1";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $total_notifications += $count;
        $stmt->close();
    }
}

echo json_encode(["new_notifications" => $total_notifications]);
$conn->close();
?>
