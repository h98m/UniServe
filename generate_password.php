<?php

define('SECURE_ACCESS', true);
require_once __DIR__ . '/config/config.php';

if (php_sapi_name() !== 'cli' && !isset($_GET['generate'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>أداة توليد كلمة المرور</title>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Cairo', sans-serif;
                background: #f5f5f5;
                padding: 50px;
                text-align: center;
            }
            .container {
                max-width: 500px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            h1 { color: #004085; margin-bottom: 20px; }
            input {
                width: 100%;
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                margin-bottom: 15px;
            }
            button {
                background: #004085;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
            }
            button:hover { background: #002b5c; }
            .result {
                margin-top: 20px;
                padding: 15px;
                background: #e8f5e9;
                border-radius: 8px;
                word-break: break-all;
                font-family: monospace;
                font-size: 12px;
            }
            .warning {
                background: #fff3cd;
                color: #856404;
                padding: 10px;
                border-radius: 8px;
                margin-top: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔐 أداة توليد كلمة المرور</h1>
            
            <form method="GET">
                <input type="password" name="password" placeholder="أدخل كلمة المرور الجديدة" required>
                <input type="hidden" name="generate" value="1">
                <button type="submit">توليد كلمة المرور المشفرة</button>
            </form>
            
            <?php if (isset($_GET['password'])): ?>
                <div class="result">
                    <strong>كلمة المرور المشفرة:</strong><br><br>
                    <?= htmlspecialchars(password_hash($_GET['password'], PASSWORD_ARGON2ID, [
                        'memory_cost' => 65536,
                        'time_cost' => 4,
                        'threads' => 3
                    ])) ?>
                </div>
                
                <div class="warning">
                    ⚠️ انسخ هذه القيمة واستخدمها في قاعدة البيانات<br>
                    ثم احذف هذا الملف فوراً!
                </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// للاستخدام من سطر الأوامر
if (php_sapi_name() === 'cli') {
    echo "أداة توليد كلمة المرور\n";
    echo "======================\n\n";
    
    if ($argc < 2) {
        echo "الاستخدام: php generate_password.php <password>\n";
        exit(1);
    }
    
    $password = $argv[1];
    $hash = password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
    
    echo "كلمة المرور المشفرة:\n";
    echo $hash . "\n\n";
    echo "استخدم هذه القيمة في قاعدة البيانات.\n";
}
?>
