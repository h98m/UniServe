<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "فشل الاتصال بقاعدة البيانات"]);
    exit;
}

if (isset($_GET['student_id'])) {
    $student_id = $conn->real_escape_string($_GET['student_id']);
    $tables = ["leaves", "student_requests", "student_transfers", "tchfed"];
    $updated = 0;

    foreach ($tables as $table) {
        $sql = "UPDATE $table SET status_updated = 0 WHERE student_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $student_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $updated++;
        }
        $stmt->close();
    }

    echo json_encode(["success" => true, "updated_tables" => $updated]);
} else {
    echo json_encode(["success" => false, "message" => "معرف الطالب غير موجود"]);
}

$conn->close();
?>
