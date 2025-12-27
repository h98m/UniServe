<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// حساب الطلبات باستخدام استعلام واحد لكل الجداول
$sql = "
    SELECT COUNT(*) AS total, 
           SUM(CASE WHEN status='في الانتظار' THEN 1 ELSE 0 END) AS pending,
           SUM(CASE WHEN status='تم الموافقة على الطلب' THEN 1 ELSE 0 END) AS completed
    FROM (
        SELECT status FROM leaves
        UNION ALL
        SELECT status FROM student_requests
        UNION ALL
        SELECT status FROM student_transfers
        UNION ALL
        SELECT status FROM tchfed
    ) AS combined
";

$result = $conn->query($sql);
$stats = $result->fetch_assoc() ?? ['total' => 0, 'pending' => 0, 'completed' => 0];

// جلب أحدث 5 طلبات
$latest_orders_sql = "
    SELECT id, student_name, request_type, status, created_at FROM (
        SELECT id, student_name, request_type, status, created_at FROM leaves
        UNION ALL
        SELECT id, student_name, request_type, status, created_at FROM student_requests
        UNION ALL
        SELECT id, name, request_type, status, created_at FROM student_transfers
        UNION ALL
        SELECT id, student_name, request_type, status, created_at FROM tchfed
    ) AS combined
    ORDER BY created_at DESC
    LIMIT 3
";

$latest_orders_result = $conn->query($latest_orders_sql) or die("خطأ في الاستعلام: " . $conn->error);

// التأكد من أن الاستعلام نجح
$latest_orders = [];
if ($latest_orders_result) {
    while ($row = $latest_orders_result->fetch_assoc()) {
        $latest_orders[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الإدارة</title>
    <link rel="stylesheet" href="cb.css">
    <script src="cb.js" defer></script>
    <style>
        /* مستطيل أحدث الطلبات مع دمج الجدول داخله */
        .latest-orders {
            background-color: #ffffff; /* اللون الأبيض */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: 1px solid #ddd; /* إضافة حدود لتحديد القسم */
        }

        /* عنوان أحدث الطلبات داخل المستطيل */
        .latest-orders h2 {
            font-size: 24px;
            color: #004aad; /* اللون الأزرق */
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* فاصل بين العنوان والجدول */
        .orders-divider {
            width: 100%;
            height: 2px;
            background-color: #004aad; /* اللون الأزرق */
            border: none;
            margin: 10px 0;
        }

        /* تنسيق الجدول داخل قسم أحدث الطلبات */
        .latest-orders table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .latest-orders th,
        .latest-orders td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        .latest-orders th {
            background: #004aad; /* اللون الأزرق */
            color: white;
            font-weight: bold;
        }

        /* تنسيق حالات الطلب */
        .pending {
            background: #ffeb3b;
            color: black;
            padding: 5px;
            border-radius: 4px;
        }

        .completed {
            background: #4caf50;
            color: white;
            padding: 5px;
            border-radius: 4px;
        }

        .rejected {
            background: #f44336;
            color: white;
            padding: 5px;
            border-radius: 4px;
        }

        /* تحسين مظهر الجدول عند التمرير */
        .latest-orders table tr:hover {
            background-color: #f1f1f1;
        }

        /* تحسين المظهر في الوضع الليلي */
        .dark-mode .latest-orders {
            background-color: #333;
            color: white;
            border: 1px solid #555;
        }

        .dark-mode .latest-orders h2 {
            color: #66ccff; /* الأزرق الفاتح في الوضع الليلي */
        }

        .dark-mode .latest-orders th {
            background: #2980b9; /* اللون الأزرق في الوضع الليلي */
        }

        .dark-mode .latest-orders td {
            color: #fff;
        }

        /* تحسين الفواصل في الوضع الليلي */
        .dark-mode .orders-divider {
            background-color: #666;
        }
        </style>

</head>
<body>
    <div class="container">
        <!-- الشريط الجانبي -->
        <aside class="sidebar">
            <div class="logo">MUC</div>
            <ul>
                <li><a href="dashboard.php">لوحة التحكم</a></li>
                <li><a href="orders.php">الطلبات</a></li>
                <li><a href="opshen.php">الإعدادات</a></li>
            </ul>
        </aside>

        <!-- المحتوى الرئيسي -->
        <main class="main-content">
            <!-- بيانات المدير -->
            <header class="admin-header">
                <div class="header-actions">
                    <button class="icon-btn mode-toggle"><i class="fas fa-moon"></i></button>
                    <button class="icon-btn"><i class="fas fa-bell"></i></button>
                </div>
                <div class="admin-info">
                    <img id="admin-image" src="default.png" alt="Admin">
                    <div class="admin-text">
                        <h3 id="admin-name">تحميل...</h3>
                        <span>مدير النظام</span>
                    </div>
                </div>
            </header>

            <!-- الإحصائيات -->
            <section class="stats">
                <?php 
                $stat_labels = ["إجمالي الطلبات" => "total", "الطلبات غير المكتملة" => "pending", "الطلبات المكتملة" => "completed"];
                foreach ($stat_labels as $label => $key): ?>
                    <div class="stat-box">
                        <h4><?php echo $label; ?></h4>
                        <p><?php echo $stats[$key]; ?></p>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- أحدث الطلبات -->
            <section class="latest-orders">
    <h2>أحدث الطلبات</h2>
    <hr class="orders-divider">
    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>اسم الطالب</th>
                <th>نوع الطلب</th>
                <th>الحالة</th>
                <th>تاريخ التقديم</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($latest_orders as $row): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                <td class="<?php echo ($row['status'] == 'تم الموافقة على الطلب' ? 'completed' : ($row['status'] == 'في الانتظار' ? 'pending' : 'rejected')); ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                </td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

        </main>
    </div>

    <script>
        // تحميل بيانات المدير عبر AJAX
        document.addEventListener("DOMContentLoaded", function () {
            fetch("admin_info.php")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("admin-name").textContent = data.name;
                    document.getElementById("admin-image").src = get_image.php?id=${data.id};
                })
                .catch(error => console.error("خطأ في تحميل بيانات المدير:", error));
        });
    </script>
</body>
</html>