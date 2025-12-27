<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

// الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من نجاح الاتصال
if ($conn->connect_error) {
    die(json_encode(["error" => "فشل الاتصال بقاعدة البيانات."]));
}

// التحقق من تمرير student_id في الرابط
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    echo json_encode(["error" => "يجب توفير معرف الطالب."]);
    exit;
}

$student_id = $conn->real_escape_string($_GET['student_id']);

// جلب بيانات الإجازة من قاعدة البيانات
$sql = "SELECT student_name, student_number, hospital, relation, medical_file, death_proof, created_at
        FROM leaves 
        WHERE student_number = '$student_id' 
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["error" => "لم يتم العثور على بيانات."]);
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
