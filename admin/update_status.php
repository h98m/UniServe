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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id']) && isset($_POST['status'])) {
    $ids = $_POST['id'];
    $statuses = $_POST['status'];

    if (count($ids) === count($statuses)) {
        $tables = ["leaves", "student_requests", "student_transfers", "tchfed"];
        $updated = 0;

        foreach ($ids as $index => $id) {
            $status = $conn->real_escape_string($statuses[$index]);
            $id = intval($id);

            foreach ($tables as $table) {
                // تحديث الحالة وإضافة إشعار التحديث
                $update_sql = "UPDATE $table SET status = '$status', status_updated = 1 WHERE id = $id";
                
                if ($conn->query($update_sql) === TRUE && $conn->affected_rows > 0) {
                    $updated++;
                    break; // بمجرد التحديث الناجح، نخرج من البحث عن هذا الطلب
                }
            }
        }

        if ($updated > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "لم يتم العثور على الطلبات أو فشل التحديث"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "عدد غير متطابق من الطلبات والحالات"]);
    }
}

$conn->close();
?>
