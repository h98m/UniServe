<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الطلبات</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(255, 255, 255);
            align-items: center;
            color: #004085;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #002147;
            font-size: 28px;
            margin-bottom: 10px;
        }
        h2 {
            color: #004085;
            font-size: 22px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #002147;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .details-button {
            background-color: #0056b3;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        .details-button:hover {
            background-color: #003d82;
        }
        .error-message {
            color: #dc3545;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>عرض الطلبات</h1>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "leave_requests";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("<p class='error-message'>فشل الاتصال بقاعدة البيانات: " . $conn->connect_error . "</p>");
        }

        if (!empty($_GET['student_id']) && !empty($_GET['student_name'])) {
            $student_id = htmlspecialchars($_GET['student_id']);
            $student_name = htmlspecialchars($_GET['student_name']);

            // استعلام لطلب البيانات من كلا الجدولين
            $stmt = $conn->prepare("SELECT id, request_type,status, created_at FROM leaves WHERE student_number = ? 
                                    UNION ALL 
                                    SELECT id, request_type,status, created_at FROM tchfed WHERE student_number = ? 
                                    UNION ALL 
                                    SELECT id, request_type,status, created_at FROM student_requests WHERE student_number = ? 
                                    UNION ALL 
                                    SELECT id, request_type,status, created_at FROM student_transfers WHERE student_number = ? 

                                    ORDER BY created_at DESC");
            $stmt->bind_param("ssss", $student_id,$student_id,$student_id, $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table>
                        <thead>
                            <tr>
                            <th>حالة الطلب </th>
                            <th>تاريخ الطلب</th>
                            <th>التفاصيل</th>
                            <th>نوع الطلب</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";

                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";


                    // تحقق من نوع الطلب واختيار الرابط المناسب لكل سجل
                    if ($row['request_type'] == 'اجازة') {
                        echo "<td><a href='details.php?request_id=" . htmlspecialchars($row['id']) . "&student_name=" . urlencode($student_name) . "&student_id=" . urlencode($student_id) . "' class='details-button'>عرض التفاصيل</a></td>";
                    } elseif ($row['request_type'] == 'طلب تخفيض القسط') {
                        echo "<td><a href='deta.php?request_id=" . htmlspecialchars($row['id']) . "&student_name=" . urlencode($student_name) . "&student_id=" . urlencode($student_id) . "' class='details-button'>عرض التفاصيل</a></td>";
                    } elseif ($row['request_type'] == 'تاييد معنون'
                    ) {
                        echo "<td><a href='db.php?request_id=" . htmlspecialchars($row['id']) . "&student_name=" . urlencode($student_name) . "&student_id=" . urlencode($student_id) . "' class='details-button'>عرض التفاصيل</a></td>";
                    } 
                     elseif ($row['request_type'] == 'تحويل الدراسة') {
                    echo "<td><a href='dba.php?request_id=" . htmlspecialchars($row['id']) . "&student_name=" . urlencode($student_name) . "&student_id=" . urlencode($student_id) . "' class='details-button'>عرض التفاصيل</a></td>";
                      } 
                    else {
                        // إذا كان نوع الطلب لا يتناسب مع الخيارات المعروفة
                        echo "<td>لا يوجد رابط</td>";
                    }
                    echo "<td>" . htmlspecialchars($row['request_type']) . "</td>";

                    // إضافة تاريخ الطلب
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='error-message'>لا توجد طلبات لهذا الطالب في كلا الجدولين.</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='error-message'>بيانات الطالب غير مكتملة.</p>";
        }

        $conn->close();
        ?>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    let studentId = "<?php echo isset($_GET['student_id']) ? $_GET['student_id'] : ''; ?>";

    if (studentId) {
        fetch('reset_notifications.php?student_id=' + encodeURIComponent(studentId))
            .then(response => response.json())
            .then(data => console.log("تم مسح الإشعارات:", data))
            .catch(error => console.error("خطأ في مسح الإشعارات:", error));
    }
});
</script>

</body>
</html>
