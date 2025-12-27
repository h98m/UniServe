<?php
session_start();

$host = "localhost";
$dbname = "my_database";
$user = "root";
$password = "";

$student_name_error = "";
$student_id_error = "";
$database_error = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $student_name = trim(htmlspecialchars($_POST['studentName']));
        $student_id = trim(htmlspecialchars($_POST['studentNumber']));
        
        // التحقق من صحة اسم الطالب
        if (!preg_match("/^[؀-ۿa-zA-Z ]+$/u", $student_name)) {
            $student_name_error = "يرجى إدخال اسم صحيح يحتوي على أحرف فقط!";
        }
        
        // التحقق من صحة الرقم الجامعي
        elseif (!preg_match("/^[0-9]+$/", $student_id)) {
            $student_id_error = "يرجى إدخال الرقم الجامعي بشكل صحيح، يجب أن يحتوي على أرقام فقط!";
        } else {
            // البحث في قاعدة البيانات
            $stmt = $conn->prepare("SELECT phone FROM students WHERE student_id = :student_id AND student_name = :student_name");
            $stmt->execute(['student_id' => $student_id, 'student_name' => $student_name]);

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['phone'] = $row['phone']; // تخزين الرقم في الجلسة
                header("Location: dashboard.php"); // إعادة التوجيه إلى صفحة dashboard
                exit();
            } else {
                $database_error = "لم يتم العثور على بيانات مطابقة!";
            }
        }
    }
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
} finally {
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - جامعة المنصور</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- إضافة قواعد CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        .container {
            width: 90%;
            max-width: 400px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            position: relative;
            z-index: 2;
        }

        .logo-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 750px;
            height: 750px;
            opacity: 0.1;
            z-index: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: rgb(0, 89, 179);
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: rgb(0, 89, 179);
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: rgb(60, 132, 204);
            outline: none;
            box-shadow: 0px 0px 8px rgba(44, 62, 80, 0.3);
        }

        /* إخفاء أزرار الزيادة والنقصان في متصفحات Webkit (Chrome, Safari) */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* إخفاء أزرار الزيادة والنقصان في Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: rgb(0, 123, 255);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover {
            background-color: #003366;
            transform: translateY(-2px);
        }

        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 8px;
            border-radius: 8px;
            font-size: 16px;
            text-align: right;
        }

        .error-message {
            color: #dc3545;
            font-size: 16px;
            margin-top: -7px;
            text-align: right;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            .logo-bg {
                width: 500px;
                height: 500px;
            }

            input, button {
                font-size: 16px;
                padding: 10px;
            }
        }

        @media (max-width: 400px) {
            .logo-bg {
                width: 300px;
                height: 300px;
            }

            h2 {
                font-size: 22px;
            }

            button {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <img src="muclogo-01.svg" alt="شعار الجامعة" class="logo-bg">
    <div class="container">
        <h2>تسجيل الدخول</h2>
        <form method="POST">
            <div class="form-group">
                <label for="studentName">اسم الطالب</label>
                <input type="text" id="studentName" name="studentName" placeholder="اسم الطالب" value="<?= htmlspecialchars($student_name ?? '') ?>" required>
                <?php if ($student_name_error): ?>
                    <p class="error-message"><?= $student_name_error ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="studentNumber">الرقم الجامعي</label>
                <input type="number" id="studentNumber" name="studentNumber" placeholder="الرقم الجامعي" value="<?= htmlspecialchars($student_id ?? '') ?>" required>
                <?php if ($student_id_error): ?>
                    <p class="error-message"><?= $student_id_error ?></p>
                <?php elseif ($database_error): ?>
                    <p class="error-message"><?= $database_error ?></p>
                <?php endif; ?>
            </div>
            <button type="submit">تسجيل الدخول</button>
        </form>
    </div>
</body>
</html>
