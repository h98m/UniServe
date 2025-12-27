<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = $_POST['otp'];
    
    // التحقق من صحة رمز OTP
    if ($user_otp == $_SESSION['otp']) {
        // إزالة الرمز من الجلسة
        unset($_SESSION['otp']);
        echo "success";
    } else {
        echo "الرمز غير صحيح";
    }
}
?>
