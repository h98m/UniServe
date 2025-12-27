<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests"; 

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام للحصول على البيانات من قاعدة البيانات
$sql = "SELECT student_name, student_number, leave_type, hospital, relation, medical_file, death_proof, created_at FROM leaves ORDER BY created_at DESC";
$result = $conn->query($sql);

// التحقق من وجود أخطاء في الاستعلام
if (!$result) {
    die("فشل في تنفيذ الاستعلام: " . $conn->error);
}

$requests = "";

// التحقق من وجود نتائج في قاعدة البيانات
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // بداية الطلب - تصميم متناسق وأنيق
        $requests .= "<div style='
            width: 90%;
            margin: 20px auto;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            direction: rtl;
            text-align: right;
            font-family: Amiri, sans-serif; /* استخدام خط عربي فاخر */
            border: 1px solid #ddd;
        '>";

        // **ترتيب المعلومات بشكل أفقي داخل صفوف**
        $requests .= "<div style='display: flex; justify-content: space-between; margin-bottom: 15px;'>"; // عرض العناصر في صف

        // عمود "نوع الإجازة" و "الاسم"
        $requests .= "<div style='flex: 1; text-align: center;'><strong>نوع الإجازة:</strong><br>" . $row['leave_type'] . "</div>"; 
        $requests .= "<div style='flex: 1; text-align: center;'><strong>الاسم:</strong><br>" . $row['student_name'] . "</div>";
        
        // عمود "الرقم الجامعي"
        $requests .= "<div style='flex: 1; text-align: center;'><strong>الرقم الجامعي:</strong><br>" . $row['student_number'] . "</div>"; 

        // **تفاصيل إضافية بناءً على نوع الإجازة**
        if ($row['leave_type'] === 'وفاة') {
            $requests .= "<div style='flex: 1; text-align: center;'><strong>القرابة:</strong><br>" . $row['relation'] . "</div>";
            if (!empty($row['death_proof'])) {
                $deathProofImage = base64_encode($row['death_proof']);
                $requests .= "<div style='flex: 1; text-align: center;'><strong>شهادة الوفاة:</strong><br>
                    <img src='data:image/jpeg;base64," . $deathProofImage . "' alt='شهادة الوفاة' style='width: 120px; height: 120px; border-radius: 8px;' />
                </div>";
            } else {
                $requests .= "<div style='flex: 1; text-align: center;'><strong>شهادة الوفاة:</strong><br>لا توجد شهادة وفاة مرفقة</div>";
            }
        }

        if ($row['leave_type'] === 'إجازة مرضية') {
            $requests .= "<div style='flex: 1; text-align: center;'><strong>اسم المستشفى:</strong><br>" . $row['hospital'] . "</div>";
            if (!empty($row['medical_file'])) {
                $medicalFileImage = base64_encode($row['medical_file']);
                $requests .= "<div style='flex: 1; text-align: center;'><strong>التقرير الطبي:</strong><br>
                    <img src='data:image/jpeg;base64," . $medicalFileImage . "' alt='التقرير الطبي' style='width: 120px; height: 120px; border-radius: 8px;' />
                </div>";
            } else {
                $requests .= "<div style='flex: 1; text-align: center;'><strong>التقرير الطبي:</strong><br>لا يوجد تقرير طبي مرفق</div>";
            }
        }

        $requests .= "</div>"; // إغلاق صف المعلومات الرئيسية

        // **التاريخ في الأسفل**
        $requests .= "<p style='color: #7f8c8d; font-size: 12px; text-align: center; margin-top: 20px;'><strong>التاريخ:</strong> " . $row['created_at'] . "</p>";

        $requests .= "</div>"; // إغلاق الطلب
    }
} else {
    $requests = "<p style='text-align: center; color: #555;'>لا توجد طلبات حتى الآن.</p>";
}

// إرسال المحتوى كـ HTML
echo $requests;

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
