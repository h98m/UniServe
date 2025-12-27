<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $studentId = $_POST['studentId'];
    $requestType = $_POST['requestType'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : NULL;
    $lostPlace = isset($_POST['lostPlace']) ? $_POST['lostPlace'] : NULL;
    $lostAction = isset($_POST['lostAction']) ? $_POST['lostAction'] : NULL;
    
    $filePath = NULL;
    if (isset($_FILES['deathProof']) && $_FILES['deathProof']['error'] == 0) {
        $fileTmp = $_FILES['deathProof']['tmp_name'];
        $fileName = basename($_FILES['deathProof']['name']);
        $targetDir = "uploads/";
        $filePath = $targetDir . $fileName;
        move_uploaded_file($fileTmp, $filePath);
    }

    $stmt = $conn->prepare("INSERT INTO requests (name, student_id, request_type, reason, lost_place, lost_action, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $studentId, $requestType, $reason, $lostPlace, $lostAction, $filePath);
    
    if ($stmt->execute()) {
        echo "تم إرسال الطلب بنجاح.";
    } else {
        echo "خطأ في إرسال الطلب: " . $stmt->error;
    }
    

}
$stmt->close();
$conn->close();

// إعادة توجيه المستخدم بعد الإرسال
header("Location: TLPAT.php");
?>
