<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if (!empty($_GET['request_id']) && !empty($_GET['type'])) {
    $request_id = intval($_GET['request_id']);
    $column = ($_GET['type'] == "medical") ? "medical_file" : "death_proof";

    $stmt = $conn->prepare("SELECT $column FROM leaves WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image);
    $stmt->fetch();

    if ($image) {
        header("Content-Type: image/jpeg"); // تغييرها حسب نوع الصورة المخزنة
        echo $image;
    } else {
        echo "لم يتم العثور على الصورة.";
    }
    $stmt->close();
} else {
    echo "طلب غير صالح.";
}

$conn->close();
?>
