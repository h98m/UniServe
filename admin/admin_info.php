<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام للحصول على بيانات المدير
$id = 1; // المدير الأساسي
$sql = "SELECT id, name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();
$conn->close();

// إرسال البيانات بصيغة JSON
echo json_encode($admin, JSON_UNESCAPED_UNICODE);
?>
