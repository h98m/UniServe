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
</head>
<body>
    
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
</body>
</html>