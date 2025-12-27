<?php
header('Content-Type: application/json');
$db = new SQLite3('users.db');

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkLogin($db, $data['username'], $data['password']);
}

function checkLogin($db, $username, $password) {
    $stmt = $db->prepare("SELECT id, phone FROM users WHERE username = :username AND password = :password");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user) {
        echo json_encode(["success" => true, "userId" => $user['id'], "phone" => $user['phone']]);
    } else {
        echo json_encode(["success" => false, "message" => "اسم المستخدم أو كلمة المرور غير صحيحة"]);
    }
}
?>
