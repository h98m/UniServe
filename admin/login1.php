<?php
session_start();
header('Content-Type: application/json');

$pdo = new PDO("mysql:host=localhost;dbname=admin", "root", "");

$instance_id = "67F05613B5F1B";
$access_token = "67f055a15747d";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT phone FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['phone'] = $user['phone'];
        
        // توليد رمز التحقق وإرساله عبر الواتساب
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        
        $whatsapp_api_url = "https://app.arrivewhats.com/api/send";
        $message = "رمز التحقق الخاص بك هو: $otp";
        
        $full_url = "$whatsapp_api_url?number=" . urlencode($user['phone']) . "&type=text&message=" . urlencode($message) . "&instance_id=$instance_id&access_token=$access_token";
        file_get_contents($full_url);
        
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
