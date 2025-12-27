<?php
// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root"; // اسم المستخدم في قاعدة البيانات
$password = ""; // كلمة المرور
$dbname = "leave_requests"; // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جمع البيانات من النموذج
    $name = $_POST['name'];
    $student_id = $_POST['studentId'];
    $shift_type = $_POST['shiftType'];
    $reason = $_POST['reason'];

    // تجهيز الاستعلام باستخدام prepared statement
    $stmt = $conn->prepare("INSERT INTO student_transfers (name, student_number, shift_type, reason) VALUES (?, ?, ?, ?)");

    if ($stmt) {
        // ربط البيانات
        $stmt->bind_param("ssss", $name, $student_id, $shift_type, $reason);

        // تنفيذ الاستعلام
        if ($stmt->execute()) {
            // إعادة التوجيه إلى الصفحة TLPAT.php مع تمرير بيانات الطالب
            header("Location: TLPAT.php?student_id=" . urlencode($student_id) . "&student_name=" . urlencode($name));
            exit();
        } else {
            echo "خطأ في تنفيذ الاستعلام: " . $stmt->error;
        }

        // إغلاق الاستعلام
        $stmt->close();
    } else {
        echo "فشل في تحضير الاستعلام: " . $conn->error;
    }
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب تحويل الدراسة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #004085;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .inline-fields {
            display: flex;
            gap: 10px;
        }
        .inline-fields .form-group {
            width: 50%;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 700;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #747474;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            transition: all 0.3s ease-in-out;
        }
        input:focus, select:focus {
            border-color: #0069d9;
            box-shadow: 0 0 8px rgba(0, 105, 217, 0.5);
            outline: none;
        }
        select {
            background-color: #fff;
            cursor: pointer;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #004085;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s ease-in-out;
        }
        button:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>طلب تحويل الدراسة</h2>
        <form method="POST" action="">
            <div class="inline-fields">
                <div class="form-group">
                    <label for="name">اسم الطالب:</label>
                    <input type="text" id="name" name="name" readonly>
                </div>
                <div class="form-group">
                    <label for="studentId">الرقم الجامعي:</label>
                    <input type="text" id="studentId" name="studentId" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="shiftType">نوع التحويل:</label>
                <select id="shiftType" name="shiftType" required>
                    <option value="">اختر نوع التحويل</option>
                    <option value="صباحي إلى مسائي">من صباحي إلى مسائي</option>
                    <option value="مسائي إلى صباحي">من مسائي إلى صباحي</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reason">سبب التحويل:</label>
                <input type="text" id="reason" name="reason" required>
            </div>
            <button type="submit">إرسال الطلب</button>
        </form>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const studentName = urlParams.get('student_name');
        const studentId = urlParams.get('student_id');

        if (studentName) {
            document.getElementById('name').value = studentName;
        }
        if (studentId) {
            document.getElementById('studentId').value = studentId;
        }
    });
    </script>
</body>
</html>
