<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if (!empty($_GET['request_id'])) {
    $request_id = intval($_GET['request_id']);

    // استعلام جلب تفاصيل الطلب
    $stmt = $conn->prepare("SELECT student_name,status,organization,request_type,created_at FROM student_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="ar">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>تفاصيل الطلب</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap">
            <style>
                body {
                    font-family: 'Tajawal', sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color:rgb(255, 255, 255);
                    text-align: center;
                }
                .container {
                    width: 100%;
                    max-width: 500px;
                    margin: 50px auto;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #002147;
                }
                .info {
                    text-align: right;
                    font-size: 18px;
                    margin: 10px 0;
                }
                .back-button {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 15px;
                    background-color: #0056b3;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .back-button:hover {
                    background-color: #003d82;
                }
                .image-preview {
                    margin-top: 10px;
                    max-width: 100%;
                    border-radius: 10px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>تفاصيل الطلب</h1>
                <p class="info"><strong>اسم الطالب:</strong> <?php echo htmlspecialchars($row['student_name']); ?></p>
                <p class="info"><strong>نوع الطلب :</strong> <?php echo htmlspecialchars($row['request_type']); ?></p>
                <p class="info"><strong>السبب :</strong> <?php echo htmlspecialchars($row['organization']); ?></p>
                <p class="info"><strong>حالة الطلب :</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                <p class="info"><strong>تاريخ الطلب :</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>

                <a href="TLPAT.php?student_id=<?php echo urlencode($_GET['student_id']); ?>&student_name=<?php echo urlencode($_GET['student_name']); ?>" class="back-button">العودة</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<p style='color: red; text-align: center;'>لم يتم العثور على الطلب.</p>";
    }
    $stmt->close();
} else {
    echo "<p style='color: red; text-align: center;'>رقم الطلب غير موجود.</p>";
}

$conn->close();
?>
