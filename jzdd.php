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
    die("الاتصال فشل: " . $conn->connect_error);
}

// التحقق من إرسال البيانات عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جمع البيانات من النموذج
    $name = $_POST['name'];
    $studentId = $_POST['studentId'];
    $organization = $_POST['organization'];

    // تجهيز استعلام SQL لإدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO student_requests (student_name, student_number, organization) 
            VALUES ('$name', '$studentId', '$organization')";

    if ($conn->query($sql) === TRUE) {
        // إعادة التوجيه بعد نجاح الإدخال
        header("Location: TLPAT.php?student_id=" . urlencode($studentId) . "&student_name=" . urlencode($name));
        exit();
    } else {
        echo "خطأ في الإدخال: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب تأييد لجهة معنونة</title>
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
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 700;
        }
        input {
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
        input:focus {
            border-color: #0069d9;
            box-shadow: 0 0 8px rgba(0, 105, 217, 0.5);
            outline: none;
        }
        .inline-fields {
            display: flex;
            gap: 10px;
        }
        .inline-fields .form-group {
            width: 50%;
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
        <h2>طلب تأييد لجهة معنونة</h2>
        <form method="POST" action="">
            <div class="form-group inline-fields">
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
                <label for="organization">الجهة الطالبة للتأييد:</label>
                <input type="text" id="organization" name="organization" required>
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
