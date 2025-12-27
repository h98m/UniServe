<?php
session_start();

// بيانات الاتصال بقاعدة البيانات
$host = "localhost";
$dbname = "my_database";
$user = "root";
$password = "";

// إنشاء الاتصال باستخدام PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// التحقق من استلام رقم الهاتف من الجلسة
if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone'];
    
    $ph1one = preg_replace('/^\+/', '', $phone); // إزالة علامة "+" إذا وجدت

    // توليد كود التحقق وإرساله
    if (!isset($_SESSION['verification_code'])) {
        $verification_code = random_int(100000, 999999); // إنشاء كود عشوائي وحفظه في متغير
        $_SESSION['verification_code'] = $verification_code; // تخزين الكود في الجلسة
    
        // إرسال الكود عبر ArriveWhats
        $instance_id = "67F38BC423234";
        $access_token = "67f055a15747d";
        $url = "https://app.arrivewhats.com/api/send?number=$ph1one&type=text&message=$verification_code&instance_id=$instance_id&access_token=$access_token";
    
        $response = file_get_contents($url);
        if ($response === false) {
            die("فشل في إرسال الكود، يرجى المحاولة لاحقًا.");
        }
    }
}

// التحقق من الكود المدخل بعد إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['n_code'])) {
    $entered_code = trim($_POST['n_code']); // تنظيف المدخلات

    if (isset($_SESSION['verification_code']) && $entered_code == $_SESSION['verification_code']) {
        // البحث عن بيانات الطالب بناءً على رقم الهاتف
        $stmt = $conn->prepare("SELECT student_id, student_name FROM students WHERE phone = :phone");
        $stmt->bindParam(":phone", $phone);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            // نجاح التحقق والعثور على الطالب
            session_unset();
            session_destroy();
            header("Location: in.html?student_id={$student['student_id']}&student_name=" . urlencode($student['student_name']));
            exit;
        } else {
            $error_message = "رقم الهاتف غير مسجل!";
        }
    } else {
        $error_message = "الكود المدخل غير صحيح. يرجى المحاولة مرة أخرى.";
    }
}
?>


<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إرسال كود التحقق</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">

    <style>
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
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            z-index: 7;
        }
        h2 {
            color: #004085; /* لون الأزرق الداكن */
            font-size: 26px;
            margin-bottom: 20px;
        }
        .phone-display {
            font-family: 'Cairo', sans-serif;

            font-weight: bold;
            font-size: 20px;
            color: #ff5f6d; /* لون مميز لعرض الرقم */
            margin-bottom: 15px;
        }
        .verification-code {
            font-size: 23px;
            color: #28a745; /* لون أخضر لكود التحقق */
            margin-bottom: 20px;
            font-weight: bold;
        }
        label {
            font-size: 20px; /* الحجم المحدد للنص */
            color: rgb(0, 0, 0); /* اللون المحدد للنص */
            margin-right: -237px; /* المسافة بين النص والحقل */
            display: inline-block; /* وضع النص بجانب الحقل */
            text-align: right; /* محاذاة النص من اليمين */
            font-weight: bold;
            margin-bottom: 10px;
        }
        .verification-code-input {
            margin-bottom: 20px;
        }
        input:focus {
            border-color:rgb(60, 132, 204);
            outline: none;
            box-shadow: 0px 0px 8px rgba(44, 62, 80, 0.3);
        }
        .verification-code-input input {
            width: 100%;
            height: 50px;
            font-size: 24px;
            text-align: center;
            border: none; /* إزالة الإطار */
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #f8f9fa; /* إضافة لون خلفية فاتح */
        }
        button {
            background-color: rgb(0, 123, 255); /* لون الأزرق الداكن */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #003366; /* تدرج من الأزرق الداكن إلى الأزرق العميق عند المرور */
        }
        .message {
            font-size: 14px;
            color: #888888;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            font-size: 16px;
            margin-top: 10px;
            text-align: center;
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


/* تقليل حجم الشعار على الشاشات الصغيرة */
@media (max-width: 768px) {
    .logo-bg {
        width: 70%;
        max-width: 500px;
    }
}

@media (max-width: 480px) {
    .logo-bg {
        width: 80%;
        max-width: 300px;
    }
}
        @media (max-width: 480px) {
            .container {
                padding: 15px;
                width: 100%;
            }
            h2 {
                font-size: 20px;
            }
            .verification-code-input input {
                height: 40px;
                font-size: 20px;
            }
            button {
                font-size: 16px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<img src="muclogo-01.svg" alt="شعار الجامعة" class="logo-bg">

<div class="container">
    <h2>تم إرسال كود التحقق</h2>
    <form action="" method="post" id="verificationForm">
        <label for="n_code"> أدخل كود التحقق : </label>
        <div class="verification-code-input">
            <!-- حقل واحد لإدخال كود التحقق -->
            <input type="text" name="n_code" id="verification_code" maxlength="6" required>
        </div>
        <?php
    if (isset($error_message)) {
        echo "<p class='error-message'>$error_message</p>";
    }
    ?>
        <button type="submit">تحقق</button>
    </form>
    <p class="message">يرجى التحقق من رسائل WhatsApp الخاصة بك.</p>
</div>
</body>
</html>
