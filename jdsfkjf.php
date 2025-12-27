<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// استعلام جلب البيانات
$sql = "SELECT id, student_name, student_number, leave_type, hospital, relation, medical_file, death_proof FROM leaves";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>اسم الطالب</th><th>رقم الطالب</th><th>نوع الإجازة</th><th>المستشفى</th><th>العلاقة</th><th>التقرير الطبي</th><th>شهادة الوفاة</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["student_name"] . "</td>";
        echo "<td>" . $row["student_number"] . "</td>";
        echo "<td>" . $row["leave_type"] . "</td>";
        echo "<td>" . ($row["hospital"] ? $row["hospital"] : '-') . "</td>";
        echo "<td>" . ($row["relation"] ? $row["relation"] : '-') . "</td>";
        echo "<td>" . ($row["medical_file"] ? '<img src="data:image/jpeg;base64,' . base64_encode($row["medical_file"]) . '" width="100"/>' : '-') . "</td>";
        echo "<td>" . ($row["death_proof"] ? '<img src="data:image/jpeg;base64,' . base64_encode($row["death_proof"]) . '" width="100"/>' : '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "لا توجد بيانات متاحة";
}

// إغلاق الاتصال
$conn->close();
?>
